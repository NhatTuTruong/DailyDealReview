@extends('backend.index')

@section('title')
    Quản lý đại lý/cửa hàng
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Đại lý/cửa hàng</li>
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
                                    <input type="text" name="key" class="form-control" value="{{ $filter['key'] }}"
                                           placeholder="Tìm kiếm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="dealer_id" onchange="this.form.submit()">
                                        <option value="0">Tất cả đại lý</option>
                                        {!! $options['dealers'] !!}
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
                        @can('member/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_member_create') }}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $members->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
