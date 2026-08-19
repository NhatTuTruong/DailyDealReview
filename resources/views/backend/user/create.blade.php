@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $user->exists ? 'Sửa user' : 'Thêm mới user' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_user') }}">User</a></li>
    <li class="breadcrumb-item active">{{ $user->exists ? 'Sửa user' : 'Thêm mới user' }}</li>
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
                        @can('user/' . ($user->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($user->exists)
                            @can('user/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_user_create') }}"/>
                            @endcan
                            @can('user/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_user_delete', $user->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_user_save', $user) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $user->name }}" label="Họ và tên"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="email" value="{{ old('email') ?: $user->email }}" label="Email"
                                       :messages="$errors->get('email')"/>
                        <x-forms.input name="phone" value="{{ old('phone') ?: $user->phone }}" label="Số điện thoại"
                                       :messages="$errors->get('phone')"/>

                        <x-forms.select name="group_id" label="Group Admin" :options="new HtmlString($option_groups)"
                                        :messages="$errors->get('group_id')"
                                        help="Select group admin to assign permission to this account"/>

                        <x-forms.upload name="avatar" value="{{ old('avatar') ?: $user->avatar }}" label="Avatar"
                                        type="image" :messages="$errors->get('avatar')"/>

                        <x-forms.switch name="status" value="{{ $user->status ?? 1 }}" label="Trạng thái"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="is_super_admin" value="{{ $user->is_super_admin ?? 1 }}" label="Is Super Admin"
                                        :messages="$errors->get('is_super_admin')"/>

                        <x-forms.input name="password" value="" label="Password"
                                       :messages="$errors->get('password')" type="password"
                                       help="If you do not change your password, Please leave this field blank"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
