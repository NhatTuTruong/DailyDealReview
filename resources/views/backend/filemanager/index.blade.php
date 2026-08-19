@extends('backend.index')

@section('title')
    Quản lý file
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Quản lý file</li>
@endsection

@section('action')

@endsection

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div id="ckfinder-widget"></div>
        </div>
    </section>
@endsection

@section('script')
    <script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
    <script>CKFinder.config({connectorPath: '/ckfinder/connector'});</script>
    <script>

        CKFinder.widget('ckfinder-widget', {
            width: '100%',
            height: 700
        });

    </script>
@endsection
