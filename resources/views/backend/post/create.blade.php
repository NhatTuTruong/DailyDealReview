@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $post->exists ? 'Sửa tin tức' : 'Thêm mới tin tức' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_post') }}">Tin tức</a></li>
    <li class="breadcrumb-item active">{{ $post->exists ? 'Sửa tin tức' : 'Thêm mới tin tức' }}</li>
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
                        @can('post/' . ($post->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($post->exists)
                            @can('post/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_post_create') }}"/>
                            @endcan
                            @can('post/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_post_delete', $post->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_post_save', $post) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $post->name }}" label="Tên bài viết"
                                       :required="true"
                                       onkeyup="changeNameToSlug('name', 'slug', false)"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="slug" value="{{ old('slug') ?: $post->slug }}" label="Slug"
                                       :messages="$errors->get('slug')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $post->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.select2 name="cat_ids[]" label="Danh mục cha"
                                         :options="new HtmlString($option_categories)"
                                         :messages="$errors->get('cat_ids')" multiple="multiple" id="cat_ids"/>
                        <x-forms.input name="created_at" id="created_at" value="{{ (old('created_at') ?: $post->created_at) ?: date('Y/m/d') }}"
                                       label="Ngày đăng" :messages="$errors->get('created_at')"/>
                        <x-forms.upload name="image" value="{{ old('image') ?: $post->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>

                        <x-forms.switch name="status" value="{{ $post->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="is_hot" value="{{ $post->is_hot ?? 1 }}" label="Nổi bật"
                                        :messages="$errors->get('is_hot')"/>

                        <x-forms.textarea name="description" :required="true"
                                          value="{{ old('description') ?: $post->description }}"
                                          label="Mô tả" :messages="$errors->get('description')"/>
                        <x-forms.textarea name="content" :required="true"
                                          value="{{ old('content') ?: $post->content }}"
                                          label="Nội dung chi tiết" editor="true"
                                          :messages="$errors->get('content')"/>

                        <x-forms.input name="meta_title" value="{{ old('meta_title') ?: $post->meta_title }}"
                                       label="Meta Title"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.input name="meta_keywords"
                                       value="{{ old('meta_keywords') ?: $post->meta_keywords }}"
                                       label="Meta Keywords"
                                       :messages="$errors->get('meta_keywords')"/>
                        <x-forms.textarea name="meta_description"
                                          value="{{ old('meta_description') ?: $post->meta_description }}"
                                          label="Meta Description" :messages="$errors->get('meta_description')"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection

@section('bottom')
    <script src="{{ asset('backend_assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('backend_assets/vendor/daterangepicker/daterangepicker.min.js') }}"></script>
    <script>

        $('#created_at').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2015,
            maxYear: 2025,
            //timePicker: true,
            locale: {
                format: 'YYYY/MM/DD'
            }
        })

    </script>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('backend_assets/vendor/daterangepicker/daterangepicker.css') }}">
@endsection