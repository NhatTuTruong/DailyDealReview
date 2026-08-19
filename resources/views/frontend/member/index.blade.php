@extends('frontend.index')

@section('content')
    <div class="page__content">
        <section class="section section--page py-0">
            <img class="section__bg d-none d-md-block" src="{{ asset('images/banner-bg.jpg') }}" alt="banner">
            <img class="banner-items d-none d-md-block" src="{{ asset('images/banner-items.png') }}" alt="banner items">
            <div class="mobile-bg d-md-none">
                <img class="mobile-bg__1" src="{{ asset('images/mobile-bg-1.jpg') }}" alt="">
                <img class="mobile-bg__2" src="{{ asset('images/mobile-bg-2.png') }}" alt="">
                <img class="mobile-bg__3" src="{{ asset('images/mobile-bg-3.png') }}" alt="">
            </div>
            <div class="container">
                <div class="section__inner">
                    <div class="section__body"></div>

                    @include('frontend.footer_2')

                </div>
            </div>
        </section>
    </div>
@endsection

@push('bottom')

@endpush
