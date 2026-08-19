@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    Cấu hình SEO
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cấu hình SEO</li>
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
                    <div class="float-right mb-3 mt-3">
                        <x-forms.button-save/>
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_setting_save') }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="settings[meta_title]" value="{{ $settings['meta_title'] ?? '' }}"
                                       label="Meta title"/>

                        <x-forms.textarea name="settings[meta_keywords]" :required="true"
                                          value="{{ $settings['meta_keywords'] ?? '' }}"
                                          label="Meta keywords"/>
                        <x-forms.textarea name="settings[meta_description]" :required="true"
                                          value="{{ $settings['meta_description'] ?? '' }}"
                                          label="Meta description"/>

                        <x-forms.textarea name="settings[meta_tag]" rows="10"
                                          value="{{ $settings['meta_tag'] ?? '' }}"
                                          label="Meta Tag"/>
                        <x-forms.textarea name="settings[tracking_code_head]" rows="10"
                                          value="{{ $settings['tracking_code_head'] ?? '' }}"
                                          label="Tracking code head"/>
                        <x-forms.textarea name="settings[tracking_code_body]" rows="10"
                                          value="{{ $settings['tracking_code_body'] ?? '' }}"
                                          label="Tracking code body"/>
                        <x-forms.textarea name="settings[tracking_code_bottom]" rows="10"
                                          value="{{ $settings['tracking_code_bottom'] ?? '' }}"
                                          label="Tracking code bottom"/>

                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
