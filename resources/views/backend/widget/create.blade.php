@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $widget->exists ? 'Sửa widget' : 'Thêm mới widget' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_widget') }}">Widget</a></li>
    <li class="breadcrumb-item active">{{ $widget->exists ? 'Sửa widget' : 'Thêm mới widget' }}</li>
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
                        @can('widget/' . ($widget->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($widget->exists)
                            @can('widget/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_widget_create') }}"/>
                            @endcan
                            @can('widget/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_widget_delete', $widget->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_widget_save', $widget) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $widget->name }}" label="Title"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="link" value="{{ old('link') ?: $widget->link }}" label="Link"
                                       :messages="$errors->get('link')"/>
                        <x-forms.upload name="image" value="{{ old('image') ?: $widget->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>
                        <x-forms.select name="position" label="Vị trí" :options="new HtmlString($options['positions'])"
                                        :messages="$errors->get('position')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $widget->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.switch name="status" value="{{ $widget->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.textarea name="description" editor="true"
                                          value="{{ old('description') ?: $widget->description }}"
                                          label="Mô tả" :messages="$errors->get('description')"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
