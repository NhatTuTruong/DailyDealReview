@extends('backend.index')

@section('title')
    Quản lý danh mục
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Danh mục</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-6 text-center">
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
                                    <select class="form-control" name="type" onchange="this.form.submit()">
                                        <option value="0">Loại danh mục</option>
                                        {!! $option_category_types !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 text-left">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xl-6">
                    <div class="float-right mb-3">
                        @can('category/edit')
                            <x-forms.button-save/>
                        @endcan
                        <x-forms.button-url title="Export" class="btn-info" icon="fa fa-file-excel"
                                            url="{{ route('backend_category_export') }}"/>
                        @can('category/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_category_create') }}"/>
                        @endcan
                        @can('category/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_category_bulk_delete')}}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_category_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
