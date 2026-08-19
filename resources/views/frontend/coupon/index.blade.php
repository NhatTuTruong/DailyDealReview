@extends('frontend.index')

@section('content')

    <section class="merchant-breadcumb">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-sm-9 hidden-xs">
                    <h1>{{ $category->name }} Coupons and Promo Codes for {{ date('F d') }}th</h1>
                    <p class="breadCumb"><a href="{{ route('home_page') }}">Home</a> &gt;
                        <a href="{{ route('category_coupons') }}">All Categories</a> &gt;
                        {{ $category->name }}
                    </p>
                </div>
            </div>
        </div>
    </section>
    <div class="container tw-m-auto">
        <section class="tw-flex xl:tw-flex-no-wrap tw-flex-wrap tw-mx-6">
            @foreach($coupons as $coupon)
                @if($loop->index < 2)
                    @include('frontend/coupon/item_feature', ['coupon' => $coupon, 'index' => $loop->index, 'show_logo' => true])
                @endif
            @endforeach
        </section>
    </div>
    <section class="main-content content-section">
        <div class="container">
            <div class="row">
                <div class="article">
                    <div class="col-md-9 col-sm-12 col-xs-12 pull-right">
                        <div class="item-bolcks">
                            <div class="tw-flex tw-border-b tw-border-orange tw-mb-2 lato">
                                @foreach(['all', 'coupons', 'deals'] as $filter_item)
                                    <a href="{{ $category->getUrl() }}@if($filter_item != 'all')?filter={{ $filter_item }}@endif"
                                       class="@if($filter == $filter_item)tw-bg-orange tw-text-white @else tw-text-grey-dark @endif tw-block tw-rounded-sm tw-rounded-b-none tw-p-2 tw-mr-1">
                                        {{ ucfirst($filter_item) }}
                                        @if($filter_item == 'coupons')
                                            ({{ $aggregated_coupons['with_code'] ?? 0 }})
                                        @elseif($filter_item == 'deals')
                                            ({{ $aggregated_coupons['without_code'] ?? 0 }})
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            @foreach($coupons as $coupon)
                                @if($loop->index > 1)
                                    @include('frontend/coupon/item', ['coupon' => $coupon, 'show_logo' => true])
                                @endif
                                @if($loop->index == 3)
                                    <div dusk="newsletter-signup"
                                         class="tw-flex tw-flex-col sm:tw-flex-row tw-items-center tw-rounded-sm tw-bg-blue tw-w-full tw-p-4 tw-mb-4">
                                        <h2 class="tw-text-white sm:tw-w-1/4">Get Todays Top Offers</h2>
                                        @include('frontend.blocks.signup_form')
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-md-12 text-right">
                                <div class="row">
                                    {!! $coupons->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <aside>
                    <div class="col-md-3 col-sm-12 col-xs-12">
                        <div class="sidebar-widget">
                            <div class="widget-content text-center">
                                <h3 class="widget-title">Featured Stores</h3>
                                <div class="row">
                                    @foreach($popular_stores as $popular_store)
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <a href="{{ $popular_store->getUrl() }}">
                                                <img src="{{ $popular_store->image }}"
                                                     alt="{{ $popular_store->name }}" loading="lazy"
                                                     class="img-responsive top-stories-logo"></a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <div class="widget-content"><h3 class="widget-title">Sub-Categories</h3>
                                <ul class="sidebar-list">
                                    @foreach($child_categories as $child_category)
                                        <li>
                                            <a href="{{ $child_category->getUrl() }}">{{ $child_category->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <div class="widget-content text-center">
                                {!! $category->description !!}
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('bottom')
@endpush