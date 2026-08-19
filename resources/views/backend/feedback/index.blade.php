@extends('backend.index')

@section('title')
    Quản lý feedback
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Feedback</li>
@endsection

@section('content')

    <hr class="mt-0">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="float-right mb-3">
                        @can('feedback/edit')
                            <x-forms.button-save/>
                        @endcan
                        @can('feedback/add')
                            <x-forms.button-url title="Thêm mới" class="btn-info" icon="fa fa-plus"
                                                url="{{ route('backend_feedback_create') }}"/>
                        @endcan
                        @can('feedback/delete')
                            <x-forms.button-bulk-delete url="{{ route('backend_feedback_bulk_delete')}}"/>
                        @endcan
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('backend_feedback_save_data_index') }}" id="formDataGrid">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                {!! $dataGrid !!}
                            </div>
                        </div>
                        {{ $feedbacks->links() }}
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
