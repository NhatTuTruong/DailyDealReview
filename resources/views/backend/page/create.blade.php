@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $page->exists ? 'Sửa trang nội dung' : 'Thêm mới trang nội dung' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_page') }}">Trang nội dung</a></li>
    <li class="breadcrumb-item active">{{ $page->exists ? 'Sửa trang nội dung' : 'Thêm mới trang nội dung' }}</li>
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
                        @can('page/' . ($page->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($page->exists)
                            @can('page/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_page_create') }}"/>
                            @endcan
                            @can('page/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_page_delete', $page->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_page_save', $page) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">
                        <x-forms.input name="name" value="{{ old('name') ?: $page->name }}" label="Title"
                                       :required="true"
                                       onkeyup="changeNameToSlug('name', 'slug', false)"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="slug" value="{{ old('slug') ?: $page->slug }}" label="Slug"
                                       :messages="$errors->get('slug')"/>

                        <x-forms.upload name="image" value="{{ old('image') ?: $page->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>
                        <x-forms.switch name="status" value="{{ $page->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.textarea name="content" editor="true"
                                          value="{{ old('content') ?: $page->content }}"
                                          label="Nội dung chi tiết" :messages="$errors->get('content')"/>
                        <x-forms.input name="meta_title" value="{{ old('meta_title') ?: $page->meta_title }}"
                                       label="Meta Title"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.input name="meta_keywords"
                                       value="{{ old('meta_keywords') ?: $page->meta_keywords }}"
                                       label="Meta Keywords"
                                       :messages="$errors->get('meta_keywords')"/>
                        <x-forms.textarea name="meta_description"
                                          value="{{ old('meta_description') ?: $page->meta_description }}"
                                          label="Meta Description" :messages="$errors->get('meta_description')"/>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
