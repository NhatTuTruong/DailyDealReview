@extends('backend.index')

@section('title')
    Quản lý group
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Group</li>
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
                        </div>
                    </form>
                </div>
                <div class="col-xl-3">
                    <div class="float-right mb-3">
                        @can('group/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_group_create') }}"/>
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
                        {{ $groups->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
