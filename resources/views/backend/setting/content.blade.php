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

                        <x-forms.input name="settings[site_name]" value="{{ $settings['site_name'] ?? '' }}"
                                       label="Site name"/>
                        <x-forms.textarea name="settings[coupon_description]" :required="true" rows="10"
                                          value="{{ $settings['coupon_description'] ?? '' }}"
                                          label="Mô tả coupons"/>
                        <x-forms.textarea name="settings[how_to_apply]" :required="true"
                                          value="{{ $settings['how_to_apply'] ?? '' }}" editor="true"
                                          label="How to apply"/>

                        <x-forms.textarea name="settings[faqs]" editor="true"
                                          value="{{ $settings['faqs'] ?? '' }}"
                                          label="FAQs"/>

                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
