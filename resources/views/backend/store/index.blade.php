@extends('backend.index')

@section('title')
    Quản lý store ({{ number_format($stores->total()) }})
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Store</li>
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
                    <h5 class="modal-title">Import Store & Offer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formImport" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="importErrorParams" class="alert alert-danger d-none">
                            <ul class="mb-0 pl-3" id="errorList"></ul>
                        </div>

                        <div class="form-group">
                            <label>Chọn file Excel (.xlsx, .xls)</label>
                            <input type="file" name="file" class="form-control-file" required
                                   accept=".xlsx, .xls, .csv">
                            <small class="text-muted">Tải file mẫu <a
                                        href="{{ asset('backend_assets/file/sample_import.xlsx') }}">tại đây</a></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitImport">
                            <span id="btnText">Import</span>
                            <span id="btnLoading" class="d-none"><i
                                        class="fa fa-spinner fa-spin"></i> Đang xử lý...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
                        // Thành công: Reload trang hoặc thông báo
                        alert(data.message);
                        location.reload();
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
