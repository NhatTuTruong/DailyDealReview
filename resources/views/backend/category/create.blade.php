@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $category->exists ? 'Sửa danh mục' : 'Thêm mới danh mục' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_category') }}">Danh mục</a></li>
    <li class="breadcrumb-item active">{{ $category->exists ? 'Sửa danh mục' : 'Thêm mới danh mục' }}</li>
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
                        @can('category/' . ($category->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($category->exists)
                            @can('category/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_category_create') }}"/>
                            @endcan
                            @can('category/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_category_delete', $category->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_category_save', $category) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $category->name }}" label="Tên danh mục"
                                       :required="true"
                                       onkeyup="changeNameToSlug('name', 'slug', false)"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="slug" value="{{ old('slug') ?: $category->slug }}" label="Slug"
                                       :messages="$errors->get('slug')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $category->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.select name="parent_id" label="Danh mục cha" :options="new HtmlString($list_category)"
                                        :messages="$errors->get('parent_id')"/>

                        <x-forms.upload name="image" value="{{ old('image') ?: $category->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>

                        <x-forms.switch name="status" value="{{ $category->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="at_home" value="{{ old('at_home') ?: $category->at_home }}"
                                        label="Hiển thị trang chủ" :messages="$errors->get('at_home')"/>

                        <x-forms.textarea name="description" editor="true"
                                          value="{{ old('description') ?: $category->description }}"
                                          label="Mô tả" :messages="$errors->get('description')"/>

                        <x-forms.input name="meta_title" value="{{ old('meta_title') ?: $category->meta_title }}"
                                       label="Meta Title"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.input name="meta_keywords"
                                       value="{{ old('meta_keywords') ?: $category->meta_keywords }}"
                                       label="Meta Keywords"
                                       :messages="$errors->get('meta_keywords')"/>
                        <x-forms.textarea name="meta_description"
                                          value="{{ old('meta_description') ?: $category->meta_description }}"
                                          label="Meta Description" :messages="$errors->get('meta_description')"/>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
