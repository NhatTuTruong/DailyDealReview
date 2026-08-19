@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $menu->exists ? 'Sửa menu' : 'Thêm mới menu' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_menu') }}">Menu</a></li>
    <li class="breadcrumb-item active">{{ $menu->exists ? 'Sửa menu' : 'Thêm mới menu' }}</li>
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
                        @can('menu/' . ($menu->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($menu->exists)
                            @can('menu/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_menu_create') }}"/>
                            @endcan
                            @can('menu/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_menu_delete', $menu->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_menu_save', $menu) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $menu->name }}" label="Title"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.select name="parent_id" label="Menu cha" :options="new HtmlString($option_menu)"
                                        :messages="$errors->get('parent_id')"/>
                        <x-forms.upload name="image" value="{{ old('image') ?: $menu->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $menu->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.switch name="status" value="{{ $menu->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.select name="page_id" label="[1]. Trang" :options="new HtmlString($option_pages)"
                                        :messages="$errors->get('page_id')"/>
                        <x-forms.select name="cat_id" label="[2]. Danh mục"
                                        :options="new HtmlString($option_categories)"
                                        :messages="$errors->get('cat_id')"/>
                        <x-forms.input name="custom_link" value="{{ old('custom_link') ?: $menu->custom_link }}"
                                       label="[3]. URL"
                                       :messages="$errors->get('custom_link')"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
