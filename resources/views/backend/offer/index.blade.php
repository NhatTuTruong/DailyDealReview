@extends('backend.index')

@section('title')
    Quản lý offer
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Offer</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-9 text-center">
                    <form action="" method="GET" class="form-filter-top-index">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" value="{{ $filter['name'] }}"
                                           placeholder="Tìm kiếm">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" id="store_id" name="store_id"
                                            onchange="this.form.submit()">
                                        <option value="0">Tất cả store</option>
                                        {!! $options['stores'] !!}
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
                            <div class="col-md-2 text-left">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xl-3">
                    <div class="float-right mb-3">
                        @can('offer/edit')
                            <x-forms.button-save/>
                        @endcan
                        @can('offer/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_offer_create') }}"/>
                        @endcan
                        @can('offer/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_offer_bulk_delete')}}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_offer_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $offers->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('bottom')

    <link rel="stylesheet" href="{{ asset('backend_assets/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend_assets/vendor/select2/select2-bootstrap4.min.css') }}">
    <link href="{{ asset('backend_assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('backend_assets/vendor/select2/select2.full.min.js') }}"></script>
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: 31px !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 31px !important;
        }

        span#select2-store_id-container {
            text-align: left;
        }
    </style>
    <script>
        $(function () {
            $('#store_id').select2({
                theme: 'bootstrap4'
            });
        });

    </script>
@endsection
