<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title') - {!! config('cms.name') !!}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="robots" content="noindex"/>

    <link href="{{ asset('backend_assets/vendor/fontawesome/all.min.css') }}" rel="stylesheet">
    {{--    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">--}}
    <link href="{{ asset('backend_assets/vendor/bootstrap/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend_assets/vendor/bootstrap/icheck-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend_assets/css/adminlte.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend_assets/vendor/overlayScrollbars/OverlayScrollbars.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{ asset('backend_assets/vendor/toastr/toastr.min.css') }}">
    <link href="{{ asset('backend_assets/css/style.css') }}?v=1.1.1" rel="stylesheet">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <script src="{{ asset('backend_assets/js/jquery.min.js') }}"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('meta')
    @yield('head')
    @yield('css')

    <script>
        var baseUrl = "{{ url('/') }}";
        var url_admin = "{{ config('cms.prefix_admin') }}";
        assetUrl = baseUrl + '/backend/';
    </script>
    <base href="{{ url('/') }}">

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    @include('backend.header')
    @include('backend.blocks.sidebar')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@yield('title')</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('backend_home') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            @yield('content')
        </section>
    </div>

    @include('backend.footer')

</div>

<script src="{{ asset('backend_assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend_assets/vendor/overlayScrollbars/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('backend_assets/vendor/bootstrap/bootstrap-switch.min.js') }}"></script>
<script src="{{ asset('backend_assets/js/adminlte.js') }}"></script>
<script src="{{ asset('backend_assets/vendor/toastr/toastr.min.js') }}"></script>

<script src="{{ asset('backend_assets/js/app.js') }}"></script>

<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
</script>

<x-forms.notification success="{{ session('success') }}" error="{{ session('error') }}"/>

@yield('script')
@yield('bottom')

</body>
</html>
