@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $store->exists ? 'Sửa cửa hàng' : 'Thêm mới cửa hàng' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_store') }}">Cửa hàng</a></li>
    <li class="breadcrumb-item active">{{ $store->exists ? 'Sửa cửa hàng' : 'Thêm mới cửa hàng' }}</li>
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

                        <x-forms.upload name="image" value="{{ old('image') ?: $store->image }}" uploadFolder="store" label="Image"
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
                        <hr>
                        <h5 class="col-sm-3 col-form-label text-bold">Offers</h5>
                        <div class="row" id="offers-container">
                            @php $offerIndex = 0; @endphp
                            @forelse($offers as $offer)
                                <div class="col-md-6 offer-item mb-3" data-index="{{ $offerIndex }}" data-offer-id="{{ $offer->id }}" draggable="true">
                                    <div class="card">
                                        <div class="card-header handle" style="cursor: move;">
                                            <span class="offer-title">{{ $offer->name ?: 'Offer #' . ($offerIndex + 1) }}</span>
                                            <span class="badge badge-info offer-order">#{{ $offerIndex + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm float-right" onclick="removeOffer(this)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="offer_id[]" value="{{ $offer->id }}">
                                            <input type="hidden" name="offer_order[]" value="{{ $offerIndex }}">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="offer_name[]" class="form-control" value="{{ $offer->name }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Code</label>
                                                <input type="text" name="offer_code[]" class="form-control offer-code" value="{{ $offer->code }}" onclick="this.select(); document.execCommand('copy');">
                                            </div>
                                            <div class="form-group">
                                                <label>Offer (VD: 50% Off)</label>
                                                <input type="text" name="offer_value[]" class="form-control" value="{{ $offer->offer }}">
                                            </div>
                                            <div class="form-group">
                                                <label>URL</label>
                                                <input type="text" name="offer_url[]" class="form-control" value="{{ $offer->url }}">
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="offer_status_{{ $offerIndex }}" name="offer_status[{{ $offerIndex }}]" {{ $offer->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="offer_status_{{ $offerIndex }}">Active</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="offer_verified_{{ $offerIndex }}" name="offer_verified[{{ $offerIndex }}]" {{ $offer->verified ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="offer_verified_{{ $offerIndex }}">Verified</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php $offerIndex++; @endphp
                            @empty
                                @php $offerIndex = 0; @endphp
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addOffer()">
                            <i class="fa fa-plus"></i> Thêm Offer
                        </button>
                        <hr>

                        <x-forms.select name="ads_user_id" label="Ads User" :options="new HtmlString($option_ads_user)"
                                        :messages="$errors->get('ads_user_id')"/>
                        <x-forms.input name="ads_email" value="{{ old('ads_email') ?: $store->ads_email }}"
                                       label="Ads Account"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.select name="ads_status" label="Ads Status"
                                        :options="new HtmlString($option_ads_status)"
                                        :messages="$errors->get('ads_status')"/>
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

                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        let offerCounter = {{ $offers->count() }};
        
        function addOffer() {
            const container = document.getElementById('offers-container');
            const index = offerCounter++;
            
            const html = `
                <div class="col-md-6 offer-item mb-3" data-index="${index}" data-offer-id="" draggable="true">
                    <div class="card">
                        <div class="card-header handle" style="cursor: move;">
                            <span class="offer-title">New Offer</span>
                            <span class="badge badge-info offer-order">#${index + 1}</span>
                            <button type="button" class="btn btn-danger btn-sm float-right" onclick="removeOffer(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="offer_id[]" value="">
                            <input type="hidden" name="offer_order[]" value="${index}">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="offer_name[]" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <label>Code</label>
                                <input type="text" name="offer_code[]" class="form-control offer-code" value="" onclick="this.select(); document.execCommand('copy');">
                            </div>
                            <div class="form-group">
                                <label>Offer (VD: 50% Off)</label>
                                <input type="text" name="offer_value[]" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" name="offer_url[]" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="offer_status_${index}" name="offer_status[${index}]" checked>
                                    <label class="custom-control-label" for="offer_status_${index}">Active</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="offer_verified_${index}" name="offer_verified[${index}]">
                                    <label class="custom-control-label" for="offer_verified_${index}">Verified</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            initDragDrop();
        }
        
        function removeOffer(btn) {
            const item = btn.closest('.offer-item');
            const hiddenInput = item.querySelector('input[name="offer_id[]"]');
            
            if (hiddenInput && hiddenInput.value) {
                if (confirm('Bạn có chắc muốn xóa offer này?')) {
                    item.remove();
                    updateOfferOrder();
                }
            } else {
                item.remove();
                updateOfferOrder();
            }
        }
        
        function updateOfferOrder() {
            const items = document.querySelectorAll('.offer-item');
            items.forEach((item, index) => {
                item.querySelector('.offer-order').textContent = '#' + (index + 1);
                item.querySelectorAll('input[name="offer_order[]"]').forEach(input => {
                    input.value = index;
                });
            });
        }
        
        function showCopyTip(el) {
            const original = el.value;
            el.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                el.style.backgroundColor = '';
            }, 300);
        }
        
        function initDragDrop() {
            const container = document.getElementById('offers-container');
            const items = container.querySelectorAll('.offer-item');
            
            items.forEach(item => {
                item.addEventListener('dragstart', dragStart);
                item.addEventListener('dragend', dragEnd);
                item.addEventListener('dragover', dragOver);
                item.addEventListener('drop', dragDrop);
                item.addEventListener('dragleave', dragLeave);
            });
        }
        
        let draggedItem = null;
        
        function dragStart(e) {
            draggedItem = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        }
        
        function dragEnd(e) {
            this.style.opacity = '1';
            draggedItem = null;
            updateOfferOrder();
        }
        
        function dragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('over');
        }
        
        function dragLeave(e) {
            this.classList.remove('over');
        }
        
        function dragDrop(e) {
            e.stopPropagation();
            this.classList.remove('over');
            
            if (draggedItem !== this) {
                const container = document.getElementById('offers-container');
                const allItems = [...container.querySelectorAll('.offer-item')];
                const draggedIndex = allItems.indexOf(draggedItem);
                const dropIndex = allItems.indexOf(this);
                
                if (draggedIndex < dropIndex) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initDragDrop();
        });
    </script>

    <style>
        .offer-item.over {
            border: 2px dashed #007bff;
            background-color: #f8f9fa;
        }
        .offer-item .handle:hover {
            background-color: #f8f9fa;
        }
        .offer-code[readonly] {
            background-color: #e9ecef;
            cursor: pointer;
        }
        .offer-code[readonly]:hover {
            background-color: #d4edda;
        }
    </style>

@endsection
