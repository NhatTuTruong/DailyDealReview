@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    Cấu hình social
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cấu hình social</li>
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

                        <x-forms.upload name="setting[og_image]" value="{{ $settings['og_image'] ?? '' }}" label="OG:image"
                                        type="image"/>
                        <x-forms.input name="settings[facebook]" value="{{ $settings['facebook'] ?? '' }}"
                                       label="Facebook"/>
                        <x-forms.input name="settings[youtube]" value="{{ $settings['youtube'] ?? '' }}"
                                       label="Youtube"/>
                        <x-forms.input name="settings[twitter]" value="{{ $settings['twitter'] ?? '' }}"
                                       label="Twitter"/>
                        <x-forms.input name="settings[instagram]" value="{{ $settings['instagram'] ?? '' }}"
                                       label="Instagram"/>
                        <x-forms.input name="settings[pinterest]" value="{{ $settings['pinterest'] ?? '' }}"
                                       label="Pinterest"/>

                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
