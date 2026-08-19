@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $store->exists ? 'Sửa store' : 'Thêm mới store' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_store') }}">Store</a></li>
    <li class="breadcrumb-item active">{{ $store->exists ? 'Sửa store' : 'Thêm mới store' }}</li>
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
                        @can('store/' . ($store->exists ? 'edit' : 'add'))
                            <x-forms.button-save/>
                        @endcan
                        @if($store->exists)
                            <x-forms.button-url title="Preview" class="btn-primary" icon="fas fa-eye"
                                                url="{{ $store->getUrl() }}" target="_blank"/>
                            @can('store/add')
                                <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                    url="{{ route('backend_store_create') }}"/>
                            @endcan
                            @can('store/delete')
                                <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                    url="{{ route('backend_store_delete', $store->id) }}"/>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <form action="{{ route('backend_store_save', $store) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $store->name }}" label="Tên store"
                                       :required="true"
                                       onkeyup="changeNameToSlug('name', 'slug', false)"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="slug" value="{{ old('slug') ?: $store->slug }}" label="Slug"
                                       :messages="$errors->get('slug')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $store->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.select2 name="cat_id" label="Danh mục" :options="new HtmlString($option_categories)"
                                         :messages="$errors->get('cat_id')"/>

                        <x-forms.upload name="image" value="{{ old('image') ?: $store->image }}" label="Image"
                                        type="image" :messages="$errors->get('image')"/>

                        <x-forms.switch name="status" value="{{ $store->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="allow_search" value="{{ $store->allow_search ?? 1 }}"
                                        label="Cho phép tìm kiếm"
                                        :messages="$errors->get('allow_search')"/>

                        <x-forms.textarea name="about_store" :required="true"
                                          value="{{ old('about_store') ?: $store->about_store }}"
                                          label="About store" editor="true"
                                          :messages="$errors->get('about_store')"/>

                        <x-forms.input name="max_offer" value="{{ old('max_offer') ?: $store->max_offer }}"
                                       label="Max Offer"
                                       :messages="$errors->get('max_offer')"/>
                        <x-forms.input name="meta_title" value="{{ old('meta_title') ?: $store->meta_title }}"
                                       label="Meta Title"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.input name="meta_keywords"
                                       value="{{ old('meta_keywords') ?: $store->meta_keywords }}"
                                       label="Meta Keywords"
                                       :messages="$errors->get('meta_keywords')"/>
                        <x-forms.textarea name="meta_description"
                                          value="{{ old('meta_description') ?: $store->meta_description }}"
                                          label="Meta Description" :messages="$errors->get('meta_description')"/>
                        <x-forms.select name="ads_user_id" label="Ads User" :options="new HtmlString($option_ads_user)"
                                        :messages="$errors->get('ads_user_id')"/>
                        <x-forms.input name="ads_email" value="{{ old('ads_email') ?: $store->ads_email }}"
                                       label="Ads Account"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.select name="ads_status" label="Ads Status"
                                        :options="new HtmlString($option_ads_status)"
                                        :messages="$errors->get('ads_status')"/>
                        <hr>
                        <x-forms.input name="af_website" value="{{ old('af_website') ?: $store->af_website }}"
                                       label="AF Website"
                                       :messages="$errors->get('af_website')"/>
                        <x-forms.select name="af_flag" label="Afiliate status"
                                        :options="new HtmlString($option_af_flag)"
                                        :messages="$errors->get('af_flag')"/>
                        <x-forms.select name="af_net" label="Afiliate Network"
                                        :options="new HtmlString($option_af_net)"
                                        :messages="$errors->get('af_net')"/>
                        <x-forms.input name="af_visit" value="{{ old('af_visit') ?: $store->af_visit }}"
                                       label="AF Visit"
                                       :messages="$errors->get('af_visit')"/>
                        <x-forms.input name="af_portal" value="{{ old('af_portal') ?: $store->af_portal }}"
                                       label="AF Portal"
                                       :messages="$errors->get('af_portal')"/>
                        <x-forms.input name="af_account" value="{{ old('af_account') ?: $store->af_account }}"
                                       label="AF Account"
                                       :messages="$errors->get('af_account')"/>
                        <x-forms.input name="commission_amount"
                                       value="{{ old('commission_amount') ?: $store->commission_amount }}"
                                       label="AF Commission"
                                       :messages="$errors->get('commission_amount')"/>
                        <x-forms.textarea name="note"
                                          value="{{ old('note') ?: $store->note }}"
                                          label="Note" :messages="$errors->get('note')"/>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">
                                Thông tin Affiliate
                            </label>
                            <div class="col-sm-9">
                                <div>
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>Commission</td>
                                            <td>{{ $store->getCommissionAmount() }}</td>
                                        </tr>
                                        <tr>
                                            <td>Website</td>
                                            <td>
                                                <a href="{{ $store->af_website }}"
                                                   target="_blank">{{ $store->af_website }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Visit</td>
                                            <td>
                                                <a href="{{ $store->af_visit }}"
                                                   target="_blank">{{ $store->af_visit }}K/month</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>View Num</td>
                                            <td>
                                                <a href="{{ $store->view_num }}"
                                                   target="_blank">{{ $store->view_num }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Link portal</td>
                                            <td>
                                                <a href="{{ $store->af_portal }}"
                                                   target="_blank">{{ $store->af_portal }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>View Offer</td>
                                            <td>
                                                <a href="{{ route('backend_offer') }}?store_id={{ $store->id }}"
                                                   target="_blank">View Offers</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
