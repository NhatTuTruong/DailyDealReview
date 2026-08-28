@extends('backend.index')
@use('\Illuminate\Support\HtmlString')

@section('title')
    {{ $post->exists ? 'Sửa tin tức' : 'Thêm mới tin tức' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('backend_post') }}">Tin tức</a></li>
    <li class="breadcrumb-item active">{{ $post->exists ? 'Sửa tin tức' : 'Thêm mới tin tức' }}</li>
@endsection

@section('content')

    <script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('backend_assets/js/globals.js') }}"></script>
    <script>CKFinder.config({connectorPath: '/ckfinder/connector'});</script>

    <section class="content">
        <div class="container-fluid">

            {{-- ===================== AI POST GENERATOR ===================== --}}
            @if(!$post->exists)
                <div class="card card-success mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-robot"></i> Tạo bài viết bằng AI từ Store
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Chọn 1 store đã có offer/coupon → AI sẽ tự động sinh bài viết tiếng Anh giới thiệu store đầy đủ,
                            thuyết phục người đọc mua hàng, kèm ảnh và mã giảm giá.
                        </p>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label font-weight-bold">Chọn Store:</label>
                            <div class="col-sm-6">
                                <select id="ai-store-select" class="form-control form-control-lg">
                                    <option value="">-- Chọn store --</option>
                                    {!! \App\Models\Store::makeListStore(0, true) !!}
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" id="btn-ai-generate" class="btn btn-success btn-lg" disabled
                                        style="width:100%">
                                    <i class="fas fa-magic"></i> Tạo bài viết bằng AI
                                </button>
                            </div>
                            <div class="col-sm-1">
                                <span id="ai-spinner" class="spinner-border spinner-border-sm text-success" role="status"
                                      style="display:none;margin-top:12px"></span>
                            </div>
                        </div>
                        <div id="ai-status" class="alert mt-2" style="display:none"></div>
                    </div>
                </div>
            @endif

            {{-- ===================== POST FORM ===================== --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ $post->exists ? 'Sửa bài viết' : 'Thêm bài viết mới' }}</h3>
                    <div class="card-tools">
                        <div class="float-right mr-2">
                            @can('post/' . ($post->exists ? 'edit' : 'add'))
                                <x-forms.button-save/>
                            @endcan
                            @if($post->exists)
                                @can('post/add')
                                    <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                        url="{{ route('backend_post_create') }}"/>
                                @endcan
                                @can('post/delete')
                                    <x-forms.button-url title="Xóa" class="btn-danger" icon="fa fa-trash"
                                                        url="{{ route('backend_post_delete', $post->id) }}"/>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
                <form action="{{ route('backend_post_save', $post) }}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal" id="formDataGrid">
                    @csrf
                    <div class="card-body">

                        <x-forms.input name="name" value="{{ old('name') ?: $post->name }}" label="Tên bài viết"
                                       :required="true"
                                       onkeyup="changeNameToSlug('name', 'slug', false)"
                                       :messages="$errors->get('name')"/>
                        <x-forms.input name="slug" value="{{ old('slug') ?: $post->slug }}" label="Slug"
                                       :messages="$errors->get('slug')"/>
                        <x-forms.input name="priority" value="{{ (old('priority') ?: $post->priority) ?: 9999 }}"
                                       label="Sắp xếp" type="number" :messages="$errors->get('priority')"/>
                        <x-forms.select2 name="cat_ids[]" label="Danh mục cha"
                                         :options="new HtmlString($option_categories)"
                                         :messages="$errors->get('cat_ids')" multiple="multiple" id="cat_ids"/>
                        <x-forms.input name="created_at" id="created_at" value="{{ (old('created_at') ?: $post->created_at) ?: date('Y/m/d') }}"
                                       label="Ngày đăng" :messages="$errors->get('created_at')"/>
                        <x-forms.upload name="image" value="{{ old('image') ?: $post->image }}" uploadFolder="blog" label="Image"
                                        type="image" :messages="$errors->get('image')"/>

                        <x-forms.switch name="status" value="{{ $post->status ?? 1 }}" label="Hiển thị"
                                        :messages="$errors->get('status')"/>
                        <x-forms.switch name="is_hot" value="{{ $post->is_hot ?? 1 }}" label="Nổi bật"
                                        :messages="$errors->get('is_hot')"/>

                        <x-forms.textarea name="description" :required="true"
                                          value="{{ old('description') ?: $post->description }}"
                                          label="Mô tả" :messages="$errors->get('description')"/>
                        <x-forms.textarea name="content" :required="true"
                                          value="{{ old('content') ?: $post->content }}"
                                          label="Nội dung chi tiết" editor="true"
                                          :messages="$errors->get('content')"/>

                        <x-forms.input name="meta_title" value="{{ old('meta_title') ?: $post->meta_title }}"
                                       label="Meta Title"
                                       :messages="$errors->get('meta_title')"/>
                        <x-forms.input name="meta_keywords"
                                       value="{{ old('meta_keywords') ?: $post->meta_keywords }}"
                                       label="Meta Keywords"
                                       :messages="$errors->get('meta_keywords')"/>
                        <x-forms.textarea name="meta_description"
                                          value="{{ old('meta_description') ?: $post->meta_description }}"
                                          label="Meta Description" :messages="$errors->get('meta_description')"/>

                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection

@section('bottom')
    <script src="{{ asset('backend_assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('backend_assets/vendor/daterangepicker/daterangepicker.min.js') }}"></script>
    <script>

        $('#created_at').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2015,
            maxYear: new Date().getFullYear() + 1,
            locale: {
                format: 'YYYY/MM/DD'
            }
        });

        // =================== AI Post Generator ===================
        (function () {
            var $aiSelect = $('#ai-store-select');
            var $aiBtn = $('#btn-ai-generate');
            var $aiStatus = $('#ai-status');
            var $aiSpinner = $('#ai-spinner');

            $aiSelect.on('change', function () {
                $aiBtn.prop('disabled', !$(this).val());
            });

            $aiBtn.on('click', function () {
                var storeId = $aiSelect.val();
                if (!storeId) return;

                $aiStatus.show().removeClass('alert-danger alert-success')
                    .addClass('alert-info')
                    .html('<i class="fas fa-spinner fa-spin"></i> &nbsp;Đang tạo bài viết bằng AI... Vui lòng chờ 20-60 giây.');
                $aiBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang tạo...');
                $aiSpinner.show();

                // Step 1: Generate content
                $.ajax({
                    url: '{{ route("backend_post_ai_generate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        store_id: storeId
                    },
                    success: function (resp) {
                        if (!resp.success) {
                            showError(resp.message || 'Lỗi không xác định');
                            return;
                        }
                        var d = resp.data;

                        // Step 2: Save
                        $aiStatus.removeClass('alert-info').addClass('alert-warning')
                            .html('<i class="fas fa-save"></i> &nbsp;Nội dung đã sinh! Đang lưu bài viết...');

                        $.ajax({
                            url: '{{ route("backend_post_ai_save") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: d.name,
                                slug: d.slug,
                                description: d.description,
                                content: d.content,
                                image: d.image,
                                meta_title: d.meta_title,
                                meta_keywords: d.meta_keywords,
                                meta_description: d.meta_description,
                                store_id: d.store_id,
                                cat_id: d.cat_id,
                            },
                            success: function (saveResp) {
                                if (!saveResp.success) {
                                    showError('Lưu thất bại: ' + (saveResp.message || ''));
                                    return;
                                }
                                $aiStatus.removeClass('alert-warning').addClass('alert-success')
                                    .html('<i class="fas fa-check-circle"></i> &nbsp;Thành công! Đang chuyển đến trang sửa bài viết...');
                                window.location.href = saveResp.redirect;
                            },
                            error: function (xhr) {
                                showError('Lỗi khi lưu: ' + (xhr.responseJSON?.message || 'Không thể lưu bài viết'));
                            }
                        });
                    },
                    error: function (xhr) {
                        showError('Lỗi khi tạo: ' + (xhr.responseJSON?.message || 'Không thể tạo bài viết'));
                    }
                });

                function showError(msg) {
                    $aiStatus.removeClass('alert-info alert-warning').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> &nbsp;' + msg);
                    $aiBtn.prop('disabled', false).html('<i class="fas fa-magic"></i> Tạo bài viết bằng AI');
                    $aiSpinner.hide();
                }
            });
        })();

    </script>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('backend_assets/vendor/daterangepicker/daterangepicker.css') }}">
@endsection
