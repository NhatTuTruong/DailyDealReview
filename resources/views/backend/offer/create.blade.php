@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $offer->exists ? 'Sửa mã giảm giá' : 'Thêm mới mã giảm giá' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_offer') }}">Mã giảm giá</a></li>
    <li class="breadcrumb-item active">{{ $offer->exists ? 'Sửa mã giảm giá' : 'Thêm mới mã giảm giá' }}</li>
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
                        @can('offer/' . ($offer->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($offer->exists)
                            @can('offer/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_offer_create') }}"/>
                            @endcan
                            @can('offer/clone')
                                <x-forms.button-url title="Clone" class="btn-info" icon="fa fa-copy"
                                                    url="{{ route('backend_offer_clone', [$offer->id]) }}"/>
                            @endcan
                            @can('offer/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_offer_delete', $offer->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_offer_save', $offer) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $offer->name }}" label="Tên offer"
                                       :required="true"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="offer" value="{{ old('offer') ?: $offer->offer }}" label="Offer"
                                       :messages="$errors->get('offer')"/>
                        <x-forms.input name="code" value="{{ old('code') ?: $offer->code }}" label="Offer code"
                                       :messages="$errors->get('code')"/>
                        <x-forms.input name="url" value="{{ old('url') ?: $offer->url }}" label="Offer url"
                                       :messages="$errors->get('url')"/>
                        <x-forms.select name="store_id" label="Store" :options="new HtmlString($option_stores)"
                                        :messages="$errors->get('store_id')"/>
                        <x-forms.textarea name="description" :required="true"
                                          value="{{ old('description') ?: $offer->description }}"
                                          help="Nếu để trống hệ thống sẽ tự động tạo mô tả"
                                          label="Mô tả" :messages="$errors->get('description')"/>


                        <x-forms.switch name="status" value="{{ $offer->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="verified" value="{{ $offer->verified ?? 1 }}" label="Verified"
                                        :messages="$errors->get('verified')"/>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
@section('bottom')

    <link rel="stylesheet" href="{{ asset('backend_assets/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/vendor/select2/select2-bootstrap4.min.css') }}">
    <link href="{{ asset('backend_assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('backend_assets/vendor/select2/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('#store_id').select2({
                theme: 'bootstrap4'
            });
        });

    </script>
@endsection
