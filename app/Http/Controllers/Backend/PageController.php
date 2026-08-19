<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private Page $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->selectedMainMenu = 'page';

        parent::__construct();

        if (!Gate::allows('page')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index()
    {
        $language = App::getLocale();
        $this->selectedSubMenu('page');
        $paginate = 20;
        $option_column_button = Page::makeOptionColumnButton();

        $pages = $this->page->where('language', $language)->orderBy('name')->paginate($paginate);

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit('backend_page_edit');
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("slug", "Slug", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='5%' ", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='3%' ");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='5%'  nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%'  nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($pages, $paginate, $pages->total());

        return view('backend.page.index', compact('pages', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('page/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Page::where('id', $key)->update($value);
        }
        return redirect()->route('backend_page')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Page $page)
    {
        if (!Gate::allows('page/' . ($page->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        return view('backend.page.create', compact('page'));
    }

    public function save(Page $page, Request $request)
    {
        if (!Gate::allows('page/' . ($page->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|alpha_dash|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
        ]);

        $language = App::getLocale();
        $name = strip_tags($request->get('name'));
        $slug = strip_tags($request->get('slug'));
        $page->name = $name;
        $page->slug = $slug ?: Str::slug($name);
        $page->description = $request->get('description');
        $page->content = $request->get('content');
        $page->image = strip_tags($request->get('image'));
        $page->status = (int)$request->get('status', 0);
        $page->meta_title = strip_tags($request->get('meta_title'));
        $page->meta_keywords = strip_tags($request->get('meta_keywords'));
        $page->meta_description = strip_tags($request->get('meta_description'));
        if (!$page->exists) {
            $page->language = $language;
        }

        try {
            $page->save();
        } catch (\Exception $ex) {
            return back()->withInput()->withErrors(['slug' => $ex->getMessage()]);
        }

        return redirect()->route('backend_page_edit', $page)->with('success', 'Cập nhật thông tin thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('page/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->page->destroy($id);
        return redirect()->to(route('backend_page'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('page/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->page->destroy($ids);
        return $this->responseJsonOk();
    }
}

