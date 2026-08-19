<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    private Menu $menu;

    public array $types = [
        'top' => 'Menu top',
        'main' => 'Menu chính',
    ];

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
        $this->selectedMainMenu = 'menu';

        parent::__construct();

        if (!Gate::allows('menu')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $language = App::getLocale();
        $menu_type = $request->get('type', '');

        if ($menu_type !== '') {
            session(['menu_type' => $menu_type]);
        }
        $menu_type = session('menu_type', 'main');
        $parent_id = $request->get('parent_id', 0);
        $menu_raw = $this->menu->where('type', $menu_type)
            ->where('language', $language)
            ->orderBy('priority')->get();
        $menus = $this->menu->showMenus($menu_raw);
        $option_positions = Util::makeHTMLOptions($this->types, $menu_type, 0, 0, 0);
        $arr_categories = Arr::prepend(Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_POST), '', 0);
        $arr_pages = Arr::prepend(Page::makeArrayListPage(), '', 0);

        $route_name = 'backend_menu_edit';

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Tiêu đề", "width='15%' nowrap");
        $clsDataGrid->addColumnSelect("page_id", "Trang", "width='5%' align='center'", $arr_pages);
        $clsDataGrid->addColumnSelect("cat_id", "Danh mục", "width='5%' align='center'", $arr_categories);
        $clsDataGrid->addColumnLabel("custom_link", "Custom URL", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("status", "Hiển thị", "width='5%' align='center'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("priority", "STT", "width='5%' align='center'");

        $dataGrid = $clsDataGrid->showDataGrid($menus);

        return view('backend.menu.index', compact('menus', 'parent_id', 'option_positions', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        if (!Gate::allows('menu/edit')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Menu::where('id', $key)->update($value);
        }
        return redirect()->route('backend_menu')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Request $request, Menu $menu)
    {
        if (!Gate::allows('menu/' . ($menu->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $parent_id = $request->get('parent_id', 0);
        $menu_type = session('menu_type', 'main');
        $option_categories = Category::makeListCategory(0, Category::CATEGORY_TYPE_POST, $menu->cat_id, true);
        $option_menu = Menu::makeListMenu(0, $menu_type, $menu->parent_id, true);
        $option_pages = Page::makeListPage($menu->page_id, true);
        return view('backend.menu.create',
            compact('menu', 'option_menu', 'option_categories', 'option_pages', 'parent_id'));
    }

    public function save(Menu $menu, Request $request)
    {
        if (!Gate::allows('menu/' . ($menu->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string'
        ]);
        $parent_id = $request->get('parent_id', 0);
        if ($menu->exists && $menu->id == $parent_id) {
            return back()->withInput()->withErrors(['parent_id' => 'Danh mục cha không thể là chính nó']);
        }
        $language = App::getLocale();
        $menu_type = session('menu_type', 'main');
        $menu->name = $request->get('name');
        $menu->image = $request->get('image');
        $menu->page_id = $request->get('page_id');
        $menu->cat_id = $request->get('cat_id');
        $menu->custom_link = $request->get('custom_link');
        $menu->parent_id = $parent_id;
        $menu->type = $menu_type;
        $menu->priority = $request->get('priority', 999999);
        $menu->status = (int)$request->get('status', 0);
        $menu->is_mega = (int)$request->get('is_mega', 0);
        $menu->language = $language;
        $menu->save();
        return redirect()->route('backend_menu')->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('menu/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->menu->destroy($id);
        return redirect()->route('backend_menu')->with('success', 'Xóa menu thành công');
    }

    public function bulkDelete(Request $request)
    {
        if (!Gate::allows('menu/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate(['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->menu->destroy($ids);
        return $this->responseJsonOk();
    }
}

