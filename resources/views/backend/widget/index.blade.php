@extends('backend.index')

@section('title')
    Quản lý widget
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Widget</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3"></div>
                <div class="col-xl-6 text-center">
                    <form action="" method="GET" class="form-filter-top-index">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="position" onchange="this.form.submit()">
                                        <option value="all">Loại widget</option>
                                        {!! $option_positions !!}
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
                <div class="col-xl-3">
                    <div class="float-right mb-3">
                        @can('widget/edit')
                            <x-forms.button-save/>
                        @endcan
                        @can('widget/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_widget_create') }}"/>
                        @endcan
                        @can('widget/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_widget_bulk_delete')}}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_widget_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $widgets->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
