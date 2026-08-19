<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->selectedMainMenu = 'user';
        $this->selectedSubMenu('user');

        parent::__construct();

        if (!Gate::allows('user')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $paginate = 20;

        $query = $this->user
            ->where('id', '<>', User::ROOT_ADMIN_ID)
            ->orderBy('id', 'desc');

        if (!$user->isSuperAdmin()) {
            $query->where('id', $user->id);
        }

        $users = $query->paginate($paginate);

        $route_name = 'backend_user_edit';
        $option_column_button = User::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='10%' nowrap ");
        $clsDataGrid->addColumnLabel("email", "Email", "width='15%' nowrap");
        $clsDataGrid->addColumnLabel("group_name", "Group", "nowrap");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='15%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($users, $paginate, $users->total());

        return view('backend.user.index', compact('users', 'dataGrid'));
    }

    public function edit(User $user)
    {
        if (!Gate::allows('user/' . ($user->exists ? 'edit' : 'add')) || $user->id == User::ROOT_ADMIN_ID) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $option_groups = Group::makeListGroup($user->group_id);
        return view('backend.user.create', compact('user', 'option_groups'));
    }

    public function save(User $user, Request $request)
    {

        if (!Gate::allows('user/' . ($user->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        if (!$user->exists) {
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        $user->name = trim(strip_tags($request->get('name')));
        $user->email = trim(strip_tags($request->get('email')));
        $user->phone = trim(strip_tags($request->get('phone')));
        $user->avatar = trim(strip_tags($request->get('avatar')));
        $password = trim(strip_tags($request->get('password')));

        if ($password) {
            if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/', $password)) {
                return back()->withInput()->withErrors(['password' => 'Mật khẩu phải lớn hơn 8 ký tự và chứa ít nhất 1 chữ viết hoa, 1 ký tự đặc biệt']);
            }
            $user->password = Hash::make($request->get('password'));;
        }

        $user->status = intval($request->get('status'));
        $user->is_super_admin = intval($request->get('is_super_admin'));
        $user->group_id = intval($request->get('group_id'));

        $user->save();

        return redirect()->route('backend_user_edit', $user)->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('user/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        if (Auth::id() === $id || $id == User::ROOT_ADMIN_ID) {
            abort('403');
        }
        $this->user->destroy($id);
        return redirect()->to(route('backend_user'))->with('success', 'Xóa user thành công');
    }
}

