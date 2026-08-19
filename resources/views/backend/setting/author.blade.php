@extends('backend.index')

@section('title')
    Cấu hình tác giả
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cấu hình tác giả</li>
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
                        <x-forms.button-save/>
                        <x-forms.button-url title="Cancel" class="bg-gray-dark" icon="fas fa-undo"
                                            url="{{ route('backend_home') }}"/>
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_setting_save') }}" method="post"
                      enctype="multipart/form-data" class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">
                        <x-forms.input name="settings[author_name]"
                                       value="{{ old('settings[author_name]') ?: ($settings['author_name'] ?? '') }}"
                                       label="Tên tác giả" :messages="$errors->get('settings[author_name]')"/>
                        <x-forms.upload name="settings[author_avatar]"
                                        value="{{ old('settings[author_avatar]') ?: ($settings['author_avatar'] ?? '') }}"
                                        label="Avatar"
                                        type="image" :messages="$errors->get('settings[author_avatar]')"/>
                        <x-forms.textarea name="settings[author_info]"
                                          value="{{ old('settings[author_info]') ?: ($settings['author_info'] ?? '') }}"
                                          label="Thông tin tác giả" editor="true"
                                          :messages="$errors->get('settings[author_info]')"/>
                        @foreach(['website', 'facebook', 'instagram', 'twitter', 'youtube'] as $social)
                            @php
                                $key_value = $settings['author_' . $social] ?? '';
                            @endphp
                            <x-forms.input name="settings[author_{{$social}}]"
                                           value="{{ $key_value }}"
                                           label="{{ ucfirst($social) }}"/>
                        @endforeach

                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
