@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $member->exists ? 'Sửa đại lý/cửa hàng' : 'Thêm mới đại lý/cửa hàng' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_member') }}">Đại lý/cửa hàng</a></li>
    <li class="breadcrumb-item active">{{ $member->exists ? 'Sửa đại lý/cửa hàng' : 'Thêm mới đại lý/cửa hàng' }}</li>
@endsection

@section('content')

    <script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('backend_assets/js/globals.js') }}"></script>
    <script>CKFinder.config({connectorPath: '/ckfinder/connector'});</script>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="float-right mb-3">
                        @can('member/' . ($member->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($member->exists)
                            @can('member/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_member_create') }}"/>
                            @endcan
                            @can('member/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_member_delete', $member->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_member_save', $member) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $member->name }}" label="Họ và tên"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="user_name" value="{{ old('user_name') ?: $member->user_name }}"
                                       label="Tên đăng nhập" :required="true"
                                       :messages="$errors->get('user_name')"/>
                        <x-forms.input name="pos_name" value="{{ old('pos_name') ?: $member->pos_name }}"
                                       label="POS name VN" :required="true"
                                       :messages="$errors->get('pos_name')"/>
                        <x-forms.input name="pos_name_en" value="{{ old('pos_name_en') ?: $member->pos_name_en }}"
                                       label="POS name EN" :required="true"
                                       :messages="$errors->get('pos_name_en')"/>

                        <x-forms.select name="parent_id" label="Thuộc quản lý của đại lý" :options="new HtmlString($list_dealer)"
                                        :messages="$errors->get('parent_id')"/>

                        <x-forms.input name="email" value="{{ old('email') ?: $member->email }}" label="Email"
                                       :messages="$errors->get('email')"/>
                        <x-forms.input name="phone" value="{{ old('phone') ?: $member->phone }}" label="Số điện thoại"
                                       :messages="$errors->get('phone')"/>

                        <x-forms.select name="province_code" label="Province" :options="new HtmlString($list_province)"
                                        :messages="$errors->get('province_code')"/>

                        <x-forms.input name="address" value="{{ old('address') ?: $member->address }}"
                                       label="Địa chỉ" :required="true"
                                       :messages="$errors->get('address')"/>
                        <x-forms.switch name="status" value="{{ $member->status ?? 1 }}" label="Trạng thái"
                                        :messages="$errors->get('status')"/>

                        <x-forms.input name="password" value="" label="Password"
                                       :messages="$errors->get('password')" type="password"
                                       help="If you do not change your password, Please leave this field blank"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
