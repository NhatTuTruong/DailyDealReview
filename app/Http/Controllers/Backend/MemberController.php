<?php

namespace App\Http\Controllers\Backend;

use App\Libs\DataGrid;
use App\Models\Group;
use App\Models\Member;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    private Member $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
        $this->selectedMainMenu = 'member';
        $this->selectedSubMenu('member');

        parent::__construct();

        if (!Gate::allows('member')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $paginate = 20;

        $filter['key'] = $request->get('key', '');
        $filter['dealer_id'] = $request->get('dealer_id', 0);

        $query = $this->member
            ->orderBy('id', 'desc');

        if (!empty($filter['key'])) {
            $query->where(function ($query) use ($filter) {
                $query->whereAny([
                    'id',
                    'user_name',
                ], $filter['key'])
                    ->orWhereAny([
                        'name',
                        'pos_name',
                        'pos_name_en',
                        'province_code',
                        'email',
                        'phone',
                        'address',
                    ], 'like', '%' . $filter['key'] . '%');
            });
        }

        if ($filter['dealer_id'] > 0) {
            $query->where('parent_id', $filter['dealer_id']);
        }

        $options['dealers'] = Member::makeListDealer($filter['dealer_id'], 0, false, true);

        $members = $query->paginate($paginate);

        $route_name = 'backend_member_edit';
        $option_column_button = Member::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='15%' nowrap ");
        $clsDataGrid->addColumnLabel("user_name", "User name", "width='15%' nowrap ");
        $clsDataGrid->addColumnLabel("email", "Email", "width='15%' nowrap");
        $clsDataGrid->addColumnLabel("type_name", "Loại", "nowrap");
        $clsDataGrid->addColumnDate("created_at", "Ngày tạo", "width='15%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($members, $paginate, $members->total());

        return view('backend.member.index', compact('members', 'options', 'filter', 'dataGrid'));
    }

    public function edit(Member $member)
    {
        if (!Gate::allows('member/' . ($member->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $list_province = Province::makeListProvince($member->province_code);
        $list_dealer = Member::makeListDealer($member->parent_id, $member->id, true);
        return view('backend.member.create', compact('member', 'list_province', 'list_dealer'));
    }

    public function save(Member $member, Request $request)
    {

        if (!Gate::allows('member/' . ($member->exists ? 'edit' : 'add'))) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $rules = [
            'name' => 'required|string',
            'user_name' => 'required|string|min:4|max:20|regex:/^[a-zA-Z0-9_]+$/|unique:members,user_name,' . $member->id,
            //'email' => 'required|email',
            'pos_name' => 'required|string',
            'address' => 'required|string',
        ];

        if (!$member->exists) {
            $rules['password'] = 'required|string|min:8';
        }

        $request->validate($rules);

        $member->name = trim(strip_tags($request->get('name')));
        $member->user_name = trim(strip_tags($request->get('user_name')));
        $member->pos_name = trim(strip_tags($request->get('pos_name')));
        $member->pos_name_en = trim(strip_tags($request->get('pos_name_en')));
        $member->email = trim(strip_tags($request->get('email')));
        $member->phone = trim(strip_tags($request->get('phone')));
        $member->province_code = trim(strip_tags($request->get('province_code')));
        $member->address = trim(strip_tags($request->get('address')));
        $member->parent_id = intval($request->get('parent_id'));
        $password = trim(strip_tags($request->get('password')));

        if ($password) {
            if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/', $password)) {
                return back()->withInput()->withErrors(['password' => 'Mật khẩu phải lớn hơn 8 ký tự và chứa ít nhất 1 chữ viết hoa, 1 ký tự đặc biệt']);
            }
            $member->password = Hash::make($request->get('password'));;
        }

        $member->status = intval($request->get('status'));

        $member->save();

        return redirect()->route('backend_member_edit', $member)->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {
        if (!Gate::allows('member/delete')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }


        $this->member->destroy($id);
        return redirect()->to(route('backend_member'))->with('success', 'Xóa member thành công');
    }
}

