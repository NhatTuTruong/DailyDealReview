@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    Cấu hình chung
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cấu hình chung</li>
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

                        <x-forms.input name="settings[site_name]" value="{{ $settings['site_name'] ?? '' }}"
                                       label="Site name"/>
                        <x-forms.input name="settings[slogan]" value="{{ $settings['slogan'] ?? '' }}"
                                       label="Slogan"/>
{{--                        <x-forms.upload name="settings[logo]" value="{{ $settings['logo'] ?? '' }}" label="Logo"--}}
{{--                                        type="image"/>--}}
                        <x-forms.upload name="settings[favicon]" value="{{ $settings['favicon'] ?? '' }}"
                                        label="Favicon"
                                        type="image"/>

                        <x-forms.switch name="settings[noindex]" value="{{ !empty($settings['noindex']) ? 1 : 0 }}"
                                        label="Chặn index"
                                        :messages="$errors->get('noindex')"/>
                        <x-forms.switch name="settings[allow_search_store]"
                                        value="{{ !empty($settings['allow_search_store']) ? 1 : 0 }}"
                                        label="Cho phép tìm kiếm store"
                                        :messages="$errors->get('allow_search_store')"/>

                        <x-forms.input name="settings[phone]" value="{{ $settings['phone'] ?? '' }}"
                                       label="Phone"/>
                        <x-forms.input name="settings[fax]" value="{{ $settings['fax'] ?? '' }}"
                                       label="Fax"/>
                        <x-forms.input name="settings[address]" value="{{ $settings['address'] ?? '' }}"
                                       label="Address"/>
                        <x-forms.input name="settings[email]" value="{{ $settings['email'] ?? '' }}"
                                       label="Email"/>
                        <x-forms.textarea name="settings[copyright]" :required="true"
                                          value="{{ $settings['copyright'] ?? '' }}"
                                          label="Copyright"/>

                        <x-forms.textarea name="settings[google_map]" :required="true"
                                          value="{{ $settings['google_map'] ?? '' }}"
                                          label="Google map"/>

                        <x-forms.textarea name="settings[about_us]" :required="true" editor="true"
                                          value="{{ $settings['about_us'] ?? '' }}"
                                          label="About us"/>

                        <x-forms.textarea name="settings[footer_info]" :required="true" editor="true"
                                          value="{{ $settings['footer_info'] ?? '' }}"
                                          label="Footer info"/>

                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
