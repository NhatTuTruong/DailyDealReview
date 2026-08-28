@extends('backend.index')

@section('title')
    Quản lý store ({{ number_format($stores->total()) }})
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Cửa hàng</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="GET" class="form-filter-top-index">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" value="{{ $filter['name'] }}"
                                           placeholder="Tìm kiếm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="cat_id" onchange="this.form.submit()">
                                        <option value="0">Tất cả danh mục</option>
                                        {!! $options['categories'] !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="status" onchange="this.form.submit()">
                                        <option value="-1">Tất cả trạng thái</option>
                                        {!! $options['status'] !!}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="af_flag" onchange="this.form.submit()">
                                        <option value="-1">Trạng thái Affiliate</option>
                                        {!! $options['af_flag'] !!}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="af_net" onchange="this.form.submit()">
                                        <option value="">All Net work</option>
                                        {!! $options['af_net'] !!}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="text" name="af_visit" class="form-control"
                                           value="{{ $filter['af_visit'] }}"
                                           placeholder="Filter visit">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="sort_by" onchange="this.form.submit()">
                                        <option value="">Sắp xếp mặc định</option>
                                        <option value="view_num" {{ ($filter['sort_by'] ?? '') == 'view_num' ? 'selected' : '' }}>Theo Lượt xem</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="sort_order" onchange="this.form.submit()">
                                        <option value="desc" {{ ($filter['sort_order'] ?? 'desc') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                        <option value="asc" {{ ($filter['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="ads_user_id" onchange="this.form.submit()">
                                        <option value="0">Ads User</option>
                                        {!! $options['ads_users'] !!}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="ads_status" onchange="this.form.submit()">
                                        <option value="">Ads Status</option>
                                        {!! $options['ads_status'] !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-left">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12">
                    <div class="float-right mb-3">
                        @can('store/edit')
                            <x-forms.button-save/>
                        @endcan
                        @can('store/import')
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#importModal">
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                                Import Excel
                            </button>
                        @endcan
                        @can('store/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_store_create') }}"/>
                        @endcan
                        @can('store/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_store_bulk_delete')}}"/>
                        @endcan
                        <button type="button" id="export-excel-btn" class="btn-info btn btn-sm">
                            <i class="fa fa-file-excel mr-1" aria-hidden="true"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_store_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $stores->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-file-excel mr-2"></i>Import Store & Offer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formImport" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="importErrorParams" class="alert alert-danger d-none">
                            <ul class="mb-0 pl-3" id="errorList"></ul>
                        </div>

                        <div id="uploadSection">
                            <div class="form-group">
                                <label>Chọn file Excel (.xlsx, .xls)</label>
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="fileInput" required
                                           accept=".xlsx, .xls, .csv" onchange="previewImport(this)">
                                    <label class="custom-file-label" for="fileInput">Chọn file...</label>
                                </div>
                                <small class="text-muted">Tải file mẫu <a
                                            href="{{ asset('backend_assets/file/sample_import.xlsx') }}" target="_blank">tại đây</a></small>
                            </div>
                        </div>

                        <div id="previewSection" class="d-none">
                            <div class="alert alert-info mb-3">
                                <h5 class="mb-2"><i class="fa fa-file-alt mr-1"></i> Xem trước dữ liệu:</h5>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h3 mb-0" id="previewStores">0</div>
                                        <small class="text-muted">Stores mới</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h3 mb-0" id="previewOffers">0</div>
                                        <small class="text-muted">Offers</small>
                                    </div>
                                </div>
                            </div>
                            <div id="storeListPreview" class="small text-muted mb-2"></div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="backToUpload()">
                                <i class="fa fa-arrow-left mr-1"></i> Chọn file khác
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitImport" disabled>
                            <span id="btnText"><i class="fa fa-upload mr-1"></i> Import</span>
                            <span id="btnLoading" class="d-none"><i
                                        class="fa fa-spinner fa-spin"></i> Đang xử lý...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Khi chọn file -> preview dữ liệu
        async function previewImport(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('{{ route('backend_store_import_preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').getAttribute('value')
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('previewStores').textContent = data.data.store_count;
                    document.getElementById('previewOffers').textContent = data.data.offer_count;

                    const listHtml = data.data.stores.slice(0, 10).map(s =>
                        `<span class="badge badge-secondary mr-1 mb-1">${s}</span>`
                    ).join('');
                    document.getElementById('storeListPreview').innerHTML =
                        '<strong>Stores:</strong> ' + listHtml +
                        (data.data.total_stores > 10 ? ` <span class="text-muted">...và ${data.data.total_stores - 10} store khác</span>` : '');

                    document.getElementById('uploadSection').classList.add('d-none');
                    document.getElementById('previewSection').classList.remove('d-none');
                    document.getElementById('btnSubmitImport').disabled = false;

                    // Update filename
                    document.querySelector('.custom-file-label').textContent = file.name;
                } else {
                    alert('Không thể đọc file: ' + data.message);
                    input.value = '';
                }
            } catch (e) {
                alert('Lỗi kết nối');
                input.value = '';
            }
        }

        function backToUpload() {
            document.getElementById('uploadSection').classList.remove('d-none');
            document.getElementById('previewSection').classList.add('d-none');
            document.getElementById('btnSubmitImport').disabled = true;
            document.getElementById('fileInput').value = '';
            document.querySelector('.custom-file-label').textContent = 'Chọn file...';
        }

        document.getElementById('formImport').addEventListener('submit', function (e) {
            e.preventDefault();

            // Reset giao diện
            const errorBox = document.getElementById('importErrorParams');
            const errorList = document.getElementById('errorList');
            const btnSubmit = document.getElementById('btnSubmitImport');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            errorBox.classList.add('d-none');
            errorList.innerHTML = '';

            // Hiệu ứng loading
            btnSubmit.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');

            // Chuẩn bị dữ liệu
            let formData = new FormData(this);

            // Gửi Request (Thay đổi URL cho đúng route của bạn)
            fetch('{{ route('backend_store_import') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').getAttribute('value')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Thành công: Hiện thông báo trong modal
                        errorBox.classList.remove('d-none');
                        errorBox.style.backgroundColor = '#d4edda';
                        errorBox.style.border = '1px solid #c3e6cb';
                        errorBox.style.color = '#155724';
                        errorList.innerHTML = '<li style="color:#155724"><i class="fa fa-check-circle mr-2"></i>' + data.message + '</li>';
                        document.getElementById('previewSection').classList.add('d-none');
                        document.getElementById('btnSubmitImport').disabled = true;
                        document.querySelector('.custom-file-label').textContent = 'Hoàn thành!';

                        // Reload table sau 1.5s
                        setTimeout(function() {
                            $('#importModal').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        // Lỗi từ Backend trả về
                        errorBox.classList.remove('d-none');

                        if (data.errors && Array.isArray(data.errors)) {
                            // Hiển thị danh sách lỗi chi tiết từng dòng
                            data.errors.forEach(err => {
                                let li = document.createElement('li');
                                li.innerText = err;
                                errorList.appendChild(li);
                            });
                        } else {
                            // Lỗi chung chung
                            let li = document.createElement('li');
                            li.innerText = data.message || 'Có lỗi xảy ra, vui lòng thử lại.';
                            errorList.appendChild(li);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorBox.classList.remove('d-none');
                    let li = document.createElement('li');
                    li.innerText = 'Lỗi kết nối Server (500). Vui lòng kiểm tra log.';
                    errorList.appendChild(li);
                })
                .finally(() => {
                    // Tắt loading
                    btnSubmit.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                });
        });
    </script>
    <script>
        document.getElementById('export-excel-btn').addEventListener('click', async function () {
            const btn = this;

            // 1. Tìm tất cả checkbox có class 'checker' và đang được check
            const selectedIds = Array.from(document.querySelectorAll('input.checker:checked'))
                .map(checkbox => checkbox.value);

            // 2. Validate phía client
            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một dự án!');
                return;
            }

            try {
                btn.disabled = true;
                btn.innerHTML = 'Đang xuất file...';

                // 3. Gửi request POST lên server
                const response = await fetch('{{ route('backend_store_export') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ids: selectedIds})
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Có lỗi xảy ra từ server');
                }

                // 4. Xử lý file trả về (Blob)
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `stores_export_${Date.now()}.xlsx`;
                document.body.appendChild(a);
                a.click();

                // Cleanup
                window.URL.revokeObjectURL(url);
                a.remove();

            } catch (error) {
                alert('Lỗi: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Xuất danh sách Store (.xlsx)';
            }
        });
    </script>
@endsection
