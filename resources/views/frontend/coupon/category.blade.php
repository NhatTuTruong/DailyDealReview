@extends('frontend.index')

@section('content')

    <section class="pages-breadcumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Browse By Category</h1>
                    <p class="breadCumb">
                        <a href="{{ route('home_page') }}">Home</a> &gt; Browse By Category
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="category-list">
        <div class="container">
            @foreach($categories as $category)
                <div class="row">
                    @foreach($category as $sub_category)
                        <div class="col-md-3 col-sm-6 cat-listItem">
                            <h3>
                                <a href="{{ $sub_category['href'] }}">{{ $sub_category['name'] }}</a>
                            </h3>
                            <ul class="cat-list">
                                @foreach($sub_category['children'] as $sub_category_lv2)
                                    <li>
                                        <a href="{{ $sub_category_lv2['href'] }}">{{ $sub_category_lv2['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('bottom')
@endpush