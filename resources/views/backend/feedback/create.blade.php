@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $feedback->exists ? 'Sửa phản hồi' : 'Thêm mới phản hồi' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_feedback') }}">Phản hồi</a></li>
    <li class="breadcrumb-item active">{{ $feedback->exists ? 'Sửa phản hồi' : 'Thêm mới phản hồi' }}</li>
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
                        @can('feedback/' . ($feedback->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($feedback->exists)
                            @can('feedback/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_feedback_create') }}"/>
                            @endcan
                            @can('feedback/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_feedback_delete', $feedback->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_feedback_save', $feedback) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">


                        <x-forms.input name="name" value="{{ old('name') ?: $feedback->name }}" label="Họ và tên"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="phone" value="{{ old('phone') ?: $feedback->phone }}" label="Phone"
                                       :messages="$errors->get('phone')"/>
                        <x-forms.input name="email" value="{{ old('email') ?: $feedback->email }}" label="Email"
                                       :messages="$errors->get('email')"/>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">
                                Ngày liên hệ
                            </label>
                            <div class="col-sm-9">
                                <b>{{ $feedback->created_at->format('d/m/Y') }}</b>
                            </div>
                        </div>
                        <x-forms.textarea name="content"
                                          value="{{ old('content') ?: $feedback->content }}"
                                          label="Nội dung liên hệ" :messages="$errors->get('content')"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
