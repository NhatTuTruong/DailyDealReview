@extends('backend.index')

@section('title')
    Cấu hình Ads
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cấu hình Ads</li>
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

                        <x-forms.textarea name="settings[ads_keyword_coupon]" :required="true"
                                          value="{{ $settings['ads_keyword_coupon'] ?? '' }}"
                                          label="Keyword coupons"/>
                        <x-forms.textarea name="settings[ads_title_coupon]" :required="true"
                                          value="{{ $settings['ads_title_coupon'] ?? '' }}"
                                          label="Tiêu đề coupons"/>

                        <x-forms.textarea name="settings[ads_description_coupon]" rows="8"
                                          value="{{ $settings['ads_description_coupon'] ?? '' }}"
                                          label="Mô tả coupons"/>
                        <x-forms.textarea name="settings[ads_keyword_discount]" rows="8"
                                          value="{{ $settings['ads_keyword_discount'] ?? '' }}"
                                          label="Keyword discount"/>
                        <x-forms.textarea name="settings[ads_title_discount]" rows="8"
                                          value="{{ $settings['ads_title_discount'] ?? '' }}"
                                          label="Tiêu đề discounts"/>
                        <x-forms.textarea name="settings[ads_description_discount]" rows="8"
                                          value="{{ $settings['ads_description_discount'] ?? '' }}"
                                          label="Mô tả discounts"/>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
