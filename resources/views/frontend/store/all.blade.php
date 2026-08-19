@extends('frontend.index')

@section('content')

    <section class="pages-breadcumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12"><h1>All Stores</h1>
                    <p class="breadCumb">
                        <a href="{{ route('home_page') }}">Home</a> &gt; All Stores
                    </p>
                </div>
            </div>
            @if(empty($letter))
                <div class="row">
                    <div class="article">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="popular-stories">
                                <h3 class="search-title">Popular Stores</h3>
                                <hr class="border-line">
                                <div class="row">
                                    @foreach($list_store_popular as $popular_store)
                                        <div class="col-md-2 col-sm-2 col-xs-4 stores-item">
                                            <a href="{{ $popular_store->getUrl() }}">
                                                <img src="{{ $popular_store->image }}" alt="{{ $popular_store->name }}"
                                                     width="180" height="110" loading="lazy"
                                                     class="sized-img img-responsive">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <section class="main-content stores-section">
        <div class="container">
            <div class="row">
                <div class="article">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="stores-pagination">
                            <div class="col-md-2">
                                <h3 class="pagination-title">Browse</h3>
                            </div>
                            <div class="col-md-10">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        @foreach(range('A', 'Z') as $letter)
                                            <li>
                                                <a href="{{ route('store_all') }}?letter={{ $letter }}">{{ $letter }}</a>
                                            </li>
                                        @endforeach
                                        <li><a href="{{ route('store_all') }}?letter=0-9">0-9</a></li>
                                    </ul>
                                </nav>
                            </div>
                            <hr class="border-line">
                        </div>
                    </div>
                </div>
            </div>
            <div class="stores-list">
                <div class="row">
                    @foreach($list_store_by_letter as $store_by_letter)
                        <div class="col-md-3 col-sm-6">
                            <ul class="stores-list my-0">
                                <li>
                                    <a href="{{ $store_by_letter->getUrl() }}">{{ $store_by_letter->name }}</a>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@push('bottom')
@endpush