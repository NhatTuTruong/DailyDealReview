<?php

namespace App\Http\Controllers\Backend;

use App\Exports\CategoriesExport;
use App\Http\Controllers\Controller;
use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    private Category $category;
    public array $category_types = Category::OPTIONS_CATEGORY;

    public function __construct(Category $category)
    {
        $this->category = $category;
        $this->selectedMainMenu = 'category';

        parent::__construct();

        if (!Gate::allows('category')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('category');

        $category_type = $request->get('type', '');
        if ($category_type !== '' && $category_type !== null) {
            session(['category_type' => (int) $category_type]);
        }
        $type = session('category_type', Category::CATEGORY_TYPE_STORE);
        $filter['name'] = $request->get('name', '');

        $query = $this->category->where('language', App::getLocale())->orderBy('name');
        if (!empty($filter['name'])) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }

        $query->where('type', $type);
        $categories = $this->category->showCategories($query->get());

        if ($categories->isEmpty()
            && $type === Category::CATEGORY_TYPE_POST
            && !$request->filled('name')
            && !$request->has('type')
            && Category::where('language', App::getLocale())
                ->where('type', Category::CATEGORY_TYPE_STORE)
                ->exists()
        ) {
            session(['category_type' => Category::CATEGORY_TYPE_STORE]);
            return redirect()->route('backend_category');
        }

        $option_category_types = Util::makeHTMLOptions($this->category_types, $type);

        $option_column_button = Category::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit('backend_category_edit');
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        //$clsDataGrid->addColumnLabel("slug", "Slug", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='5%'", ["Không", "Có"]);
        $clsDataGrid->addColumnSelect("at_home", "Hiển thị trang chủ", "width='10%'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='5%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($categories);

        return view('backend.category.index',
            compact(
                'categories',
                'option_category_types',
                'filter',
                'dataGrid'
            )
        );
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('category/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Category::where('id', $key)->update($value);
        }
        return redirect()->route('backend_category')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Request $request, Category $category)
    {
        if (!Gate::allows('category/' . ($category->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $type = session('category_type', Category::CATEGORY_TYPE_STORE);
        $list_category = Category::makeListCategory(0, $type, $category->parent_id, true);
        return view('backend.category.create', compact('category', 'list_category', 'type'));
    }

    public function save(Category $category, Request $request)
    {
        if (!Gate::allows('category/' . ($category->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|alpha_dash|unique:categories,slug,' . $category->id,
            'priority' => 'integer',
        ]);

        $parent_id = intval($request->get('parent_id', 0));
        if ($category->exists && $category->id == $parent_id) {
            return back()->withInput()->withErrors(['parent_id' => 'Danh mục cha không thể là chính nó']);
        }

        $name = strip_tags($request->get('name'));
        $slug = strip_tags($request->get('slug'));
        $category->name = $name;
        $category->slug = $slug ?: Str::slug($name);
        $category->description = $request->get('description');
        $category->content = $request->get('content');
        $category->image = $request->get('image');
        $category->parent_id = $parent_id;
        $category->priority = intval($request->get('priority', 0));
        $category->status = intval($request->get('status', 0));
        $category->at_home = intval($request->get('at_home', 0));

        $category->meta_title = strip_tags($request->get('meta_title'));
        $category->meta_keywords = strip_tags($request->get('meta_keywords'));
        $category->meta_description = strip_tags($request->get('meta_description'));

        if (!$category->exists) {
            $category->type = session('category_type', Category::CATEGORY_TYPE_STORE);
            $category->language = App::getLocale();;
        }

        try {
            $category->save();
        } catch (\Exception $ex) {
            return back()->withInput()->withErrors(['slug' => $ex->getMessage()]);
        }

        return redirect()->route('backend_category_edit', $category)->with('success', 'Cập nhật thông tin thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('category/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->category->destroy($id);
        return redirect()->route('backend_category')->with('success', 'Xóa danh mục thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('category/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->category->destroy($ids);
        return $this->responseJsonOk();
    }

    public function export(Request $request)
    {
        return Excel::download(new CategoriesExport, 'categories.xlsx');
    }
}
