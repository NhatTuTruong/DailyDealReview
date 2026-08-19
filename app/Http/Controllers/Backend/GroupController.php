<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
        $this->selectedMainMenu = 'user';
        $this->selectedSubMenu('group');

        parent::__construct();

        if (!Gate::allows('group')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $query = $this->group->orderBy('id', 'desc');

        $groups = $query->paginate(20);

        $paginate = 20;
        $route_name = 'backend_group_edit';
        $option_column_button = Group::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "nowrap");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='15%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($groups, $paginate, $groups->total());

        return view('backend.group.index', compact('groups', 'dataGrid'));
    }

    public function edit(Group $group)
    {
        if (!Gate::allows('group/' . ($group->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
        $permission_configs = config('permission');
        return view('backend.group.create', compact('group', 'permission_configs'));
    }

    public function save(Group $group, Request $request)
    {
        if (!Gate::allows('group/' . ($group->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $request->validate([
            'name' => 'required|string|unique:groups,name,' . $group->id,
            'permission' => 'required|array',
        ]);

        $permission_configs = config('permission');
        $permission_data = $this->_processPermission([], $permission_configs, $request->get('permission'));

        $group->name = strip_tags($request->get('name'));
        $group->permission_data = $permission_data;

        $group->save();

        return redirect()->route('backend_group_edit', $group)->with('success', 'Cập nhật thành công');
    }

    protected function _processPermission($permission_data, $permission_map, $permission_input, $prefix = '')
    {
        foreach ($permission_map as $perm_key => $perm_data) {
            if (!isset($permission_input[$perm_key])) {
                continue;
            }

            $permission_data[] = $prefix . $perm_key;

            if (is_array($perm_data) && !empty($perm_data['items']) && is_array($perm_data['items']) && count($perm_data['items']) > 0) {
                $permission_data = $this->_processPermission($permission_data, $perm_data['items'], $permission_input[$perm_key], $prefix . $perm_key . '/');
            }
        }

        return $permission_data;
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('group/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
        $this->group->destroy($id);
        return redirect()->to(route('backend_group'))->with('success', 'Xóa thành công');
    }
}

