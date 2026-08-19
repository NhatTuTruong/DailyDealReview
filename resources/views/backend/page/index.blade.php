@extends('backend.index')

@section('title')
    Quản lý trang nội dung
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Trang nội dung</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3"></div>
                <div class="col-xl-6 text-center">
                </div>
                <div class="col-xl-3">
                    <div class="float-right mb-3">
                        @can('page/edit')
                            <x-forms.button-save/>
                        @endcan
                        @can('page/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_page_create') }}"/>
                        @endcan
                        @can('page/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_page_bulk_delete')}}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_page_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $pages->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
