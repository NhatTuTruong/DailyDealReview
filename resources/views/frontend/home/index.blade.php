@extends('frontend.index')

@section('content')
    <main id="primary" class="site-main">
        <section id="ascendoor_news_tags_section" class="tag-section">
            <div class="ascendoor-wrapper">
                <div class="tag-section-wrapper">
                    <strong>#Tags:</strong>
                    <ul>
                        @foreach($top_tags as $top_tag)
                            <li>
                                <a href="{{ route('post_tag', $top_tag['tag']) }}">{{ $top_tag['display'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
        <section id="ascendoor_news_flash_news_section" class="flash-news-ticker">
            <div class="ascendoor-wrapper">
                <div class="flash-news-ticker-wrapper">
                    <div class="title-part">
                        <div class="title-wrap">
                                <span class="flash-loader">
                                    <svg viewBox="0 0 512 512">
                                        <polygon points="96,288 243,288 191.9,480 416,224 269,224 320,32 "/>
                                    </svg>
                                </span>
                            <span class="flash-title">Flash News </span>
                        </div>
                    </div>
                    <div class="flash-news-part" dir="ltr">
                        <div class="marquee flash-news-slide" data-speed="250">
                            @foreach($list_latest_post as $latest_post)
                                <div class="mag-post-title-wrapper">
                                    <div class="mag-post-title-wrap">
                                        <span class="flash-img">
                                            <img width="40" height="40"
                                                 src="{{ $latest_post->image }}"
                                                 class="attachment-40x40 size-40x40 wp-post-image"
                                                 alt="{{ $latest_post->name }}"
                                                 decoding="async"/> <span class="flash-number">{{ $loop->index + 1 }}</span>
                                        </span>
                                        <span class="flash-title">
                                            <a href="{{ $latest_post->getUrl() }}">{{ $latest_post->name }}</a></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="ascendoor_news_banner_section"
                 class="banner-section magazine-frontpage-section banner-section-style-2 banner-grid-slider">
            <div class="ascendoor-wrapper">
                <div class="banner-section-wrapper">
                    <div class="slider-part">
                        <div class="banner-slider magazine-carousel-slider-navigation">
                            @foreach($list_latest_post as $latest_post)
                                @if($loop->index < 5)
                                    <div class="mag-post-single banner-grid-single has-image tile-design">
                                        <div class="mag-post-img">
                                            <a href="{{ $latest_post->getUrl() }}">
                                                <img width="1280" height="853"
                                                     src="{{ $latest_post->image }}"
                                                     class="attachment-full size-full wp-post-image"
                                                     alt="{{ $latest_post->name }}" decoding="async"
                                                     fetchpriority="high"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category with-background">
                                                @foreach($latest_post->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h3 class="mag-post-title">
                                                <a href="{{ $latest_post->getUrl() }}">{{ $latest_post->name }}</a>
                                            </h3>
                                            @include('frontend.post.post_meta', ['post' => $latest_post])
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="editors-pick-part">
                        <div class="section-header">
                            <h3 class="section-title">Editor Pick</h3>
                        </div>
                        <div class="editors-pick-wrapper">
                            @foreach($list_latest_post as $latest_post)
                                @if($loop->index > 4 && $loop->index < 9)
                                    <div class="mag-post-single banner-gird-single has-image">
                                        <div class="mag-post-img">
                                            <a href="{{ $latest_post->getUrl() }}">
                                                <img width="1280" height="772"
                                                     src="{{ $latest_post->image }}"
                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                     alt="{{ $latest_post->name }}" decoding="async"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category">
                                                @foreach($latest_post->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h4 class="mag-post-title">
                                                <a href="{{ $latest_post->getUrl() }}">{{ $latest_post->name }}</a>
                                            </h4>
                                            @include('frontend.post.post_meta', ['post' => $latest_post])
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="below-banner-widgets-section ascendoor-widget-area">
            <div class="ascendoor-wrapper">
                @if($list_category_post->has(0))
                    @php($category_post_0 = $list_category_post->get(0))
                    <section id="ascendoor_news_posts_carousel_widget-3"
                             class="widget ascendoor-widget magazine-post-carousel-section">
                        <div class="section-header">
                            <h3 class="section-title">{{ $category_post_0->name }}</h3>
                            <a href="{{ $category_post_0->getUrl() }}"
                               class="mag-view-all-link">View All </a>
                        </div>
                        <div class="magazine-section-body">
                            <div class="magazine-post-carousel-section-wrapper post-carousel magazine-carousel-slider-navigation">
                                @foreach($category_post_0->posts as $post_cat1)
                                    <div class="mag-post-single has-image tile-design">
                                        <div class="mag-post-img">
                                            <a href="{{ $post_cat1->getUrl() }}">
                                                <img width="1280" height="853"
                                                     src="{{ $post_cat1->image }}"
                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                     alt="{{ $post_cat1->name }}"
                                                     decoding="async"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category with-background">
                                                @foreach($post_cat1->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h3 class="mag-post-title">
                                                <a href="{{ $post_cat1->getUrl() }}">{{ $post_cat1->name }}</a>
                                            </h3>
                                            @include('frontend.post.post_meta', ['post' => $post_cat1])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="post-carousel_dots">
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
                @if($list_category_post->has(1))
                    @php($category_post_1 = $list_category_post->get(1))
                    <section id="ascendoor_news_magazine_tile_list_widget-3"
                             class="widget ascendoor-widget magazine-tile-list-section style-1">
                        <div class="section-header">
                            <h3 class="section-title">{{ $category_post_1->name }}</h3>
                            <a href="{{ $category_post_1->getUrl() }}"
                               class="mag-view-all-link">View All </a>
                        </div>
                        <div class="magazine-section-body">
                            <div class="magazine-tile-list-section-wrapper">
                                @if($category_post_1->posts->has(0))
                                    @php($post_cat1_first = $category_post_1->posts->first())
                                    <div class="mag-post-single has-image tile-design">
                                        <div class="mag-post-img">
                                            <a href="{{ $post_cat1_first->getUrl() }}">
                                                <img width="1280" height="853"
                                                     src="{{ $post_cat1_first->image }}"
                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                     alt="{{ $post_cat1_first->name }}"
                                                     decoding="async"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category with-background">
                                                @foreach($post_cat1_first->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h3 class="mag-post-title">
                                                <a href="{{ $post_cat1_first->getUrl() }}">{{ $post_cat1_first->name }}</a>
                                            </h3>
                                            @include('frontend.post.post_meta', ['post' => $post_cat1_first])
                                            <div class="mag-post-excerpt">
                                                <p>{{ \App\Libs\Util::limitContentWords($post_cat1_first->description) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @foreach($category_post_1->posts as $post_cat1)
                                    @if($loop->index > 0 and $loop->index < 4)
                                        <div class="mag-post-single has-image list-design">
                                            <div class="mag-post-img">
                                                <a href="{{ $post_cat1->getUrl() }}">
                                                    <img width="1280" height="1024"
                                                         src="{{ $post_cat1->image }}"
                                                         class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                         alt="{{ $post_cat1->name }}"
                                                         decoding="async"/> </a>
                                            </div>
                                            <div class="mag-post-detail">
                                                <div class="mag-post-category ">
                                                    @foreach($post_cat1->categories as $cat)
                                                        <a href="{{ $cat->getUrl() }}"
                                                           style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                    @endforeach
                                                </div>
                                                <h3 class="mag-post-title">
                                                    <a href="{{ $post_cat1->getUrl() }}">{{ $post_cat1->name }}</a>
                                                </h3>
                                                @include('frontend.post.post_meta', ['post' => $post_cat1])
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
        <div class="main-widget-section">
            <div class="ascendoor-wrapper">
                <div class="main-widget-section-wrap frontpage-right-sidebar ">
                    <div class="primary-widgets-section ascendoor-widget-area">
                        @if($list_category_post->has(2))
                            @php($category_post_2 = $list_category_post->get(2))
                            <section id="ascendoor_news_magazine_grid_widget-3"
                                     class="widget ascendoor-widget magazine-grid-section style-1">
                                <div class="section-header">
                                    <h3 class="section-title">{{ $category_post_2->name }}</h3>
                                    <a href="{{ $category_post_2->getUrl() }}" class="mag-view-all-link">
                                        View All </a>
                                </div>
                                <div class="magazine-section-body">
                                    <div class="magazine-grid-section-wrapper">
                                        @foreach($category_post_2->posts as $post_cat2)
                                            @if($loop->index < 4)
                                                <div class="mag-post-single has-image">
                                                    <div class="mag-post-img">
                                                        <a href="{{ $post_cat2->getUrl() }}">
                                                            <img width="1279" height="854"
                                                                 src="{{ $post_cat2->image }}"
                                                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                                 alt="{{ $post_cat2->name }}" decoding="async"/> </a>
                                                    </div>
                                                    <div class="mag-post-detail">
                                                        <div class="mag-post-category">
                                                            @foreach($post_cat2->categories as $cat)
                                                                <a href="{{ $cat->getUrl() }}"
                                                                   style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                            @endforeach
                                                        </div>
                                                        <h3 class="mag-post-title">
                                                            <a href="{{ $post_cat2->getUrl() }}">{{ $post_cat2->name }}</a>
                                                        </h3>
                                                        @include('frontend.post.post_meta', ['post' => $post_cat2])
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        @if($list_category_post->has(3))
                            @php($category_post_3 = $list_category_post->get(3))
                            <section id="ascendoor_news_magazine_list_widget-3"
                                     class="widget ascendoor-widget magazine-list-section style-1">
                                <div class="section-header">
                                    <h3 class="section-title">{{ $category_post_3->name }}</h3>
                                    <a href="{{ $category_post_3->getUrl() }}" class="mag-view-all-link">
                                        View All </a>
                                </div>
                                <div class="magazine-section-body">
                                    <div class="magazine-list-section-wrapper">
                                        @foreach($category_post_3->posts as $post_cat3)
                                            @if($loop->index < 3)
                                                <div class="mag-post-single has-image list-design">
                                                    <div class="mag-post-img">
                                                        <a href="{{ $post_cat3->getUrl() }}">
                                                            <img width="1280" height="772"
                                                                 src="{{ $post_cat3->image }}"
                                                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                                 alt="{{ $post_cat3->name }}" decoding="async"/>
                                                        </a>
                                                    </div>
                                                    <div class="mag-post-detail">
                                                        <div class="mag-post-category">
                                                            @foreach($post_cat3->categories as $cat)
                                                                <a href="{{ $cat->getUrl() }}"
                                                                   style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                            @endforeach
                                                        </div>
                                                        <h3 class="mag-post-title">
                                                            <a href="{{ $post_cat3->getUrl() }}">{{ $post_cat3->name }}</a>
                                                        </h3>
                                                        @include('frontend.post.post_meta', ['post' => $post_cat3])
                                                        <div class="mag-post-excerpt">
                                                            <p>{{ \App\Libs\Util::limitContentWords($post_cat3->description) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section id="ascendoor_news_magazine_double_category_widget-3"
                                 class="widget ascendoor-widget magazine-double-category-section style-1">
                            <div class="magazine-section-body">
                                <div class="magazine-double-category-section-wrapper">
                                    @foreach($list_category_post as $category_post)
                                        @if($loop->index > 3 && $loop->index < 6)
                                            <div class="magazine-category-single">
                                                <div class="section-header">
                                                    <h3 class="section-title">{{ $category_post->name }}</h3>
                                                </div>
                                                @if($category_post->posts->isNotEmpty())
                                                    @php($first_post_in_cat = $category_post->posts->first())
                                                    <div class="mag-post-single has-image tile-design">
                                                        <div class="mag-post-img">
                                                            <a href="{{ $first_post_in_cat->getUrl() }}">
                                                                <img width="1280" height="853"
                                                                     src="{{ $first_post_in_cat->image }}"
                                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                                     alt="{{ $first_post_in_cat->name }}"
                                                                     decoding="async"/>
                                                            </a>
                                                        </div>
                                                        <div class="mag-post-detail">
                                                            <div class="mag-post-category with-background">
                                                                @foreach($first_post_in_cat->categories as $cat)
                                                                    <a href="{{ $cat->getUrl() }}"
                                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                                @endforeach
                                                            </div>
                                                            <h3 class="mag-post-title">
                                                                <a href="{{ $first_post_in_cat->getUrl() }}">{{ $first_post_in_cat->name }}</a>
                                                            </h3>
                                                            @include('frontend.post.post_meta', ['post' => $first_post_in_cat])
                                                        </div>
                                                    </div>
                                                    @foreach($category_post->posts as $post)
                                                        @if($loop->index > 0 && $loop->index < 4)
                                                            <div class="mag-post-single has-image list-design">
                                                                <div class="mag-post-img">
                                                                    <a href="{{ $post->getUrl() }}">
                                                                        <img width="1152" height="768"
                                                                             src="{{ $post->image }}"
                                                                             class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                                             alt="{{ $post->name }}" decoding="async"/>
                                                                    </a>
                                                                </div>
                                                                <div class="mag-post-detail">
                                                                    <div class="mag-post-category ">
                                                                        @foreach($post->categories as $cat)
                                                                            <a href="{{ $cat->getUrl() }}"
                                                                               style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                                        @endforeach
                                                                    </div>
                                                                    <h3 class="mag-post-title">
                                                                        <a href="{{ $post->getUrl() }}">{{ $post->name }}</a>
                                                                    </h3>
                                                                    @include('frontend.post.post_meta', ['post' => $post])
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="secondary-widgets-section ascendoor-widget-area">
                        @if($list_category_post->has(6))
                            @php($category_post_6 = $list_category_post->get(6))
                            <section id="ascendoor_news_magazine_small_list_widget-3"
                                     class="widget ascendoor-widget magazine-small-list-section style-1">
                                <div class="section-header">
                                    <h3 class="section-title">{{ $category_post_6->name }}</h3>
                                    <a href="{{ $category_post_6->getUrl() }}"
                                       class="mag-view-all-link">
                                        View All </a>
                                </div>
                                <div class="magazine-section-body">
                                    <div class="magazine-list-section-wrapper">
                                        @foreach($category_post_6->posts as $post_cat6)
                                            @if($loop->index < 4)
                                                <div class="mag-post-single has-image list-design">
                                                    <div class="mag-post-img">
                                                        <a href="{{ $post_cat6->getUrl() }}">
                                                            <img width="1280" height="853"
                                                                 src="{{ $post_cat6->image }}"
                                                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                                 alt="{{ $post_cat6->name }}" decoding="async"/> </a>
                                                    </div>
                                                    <div class="mag-post-detail">
                                                        <div class="mag-post-detail-inner">
                                                            <div class="mag-post-category">
                                                                @foreach($post_cat6->categories as $cat)
                                                                    <a href="{{ $cat->getUrl() }}"
                                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                                @endforeach
                                                            </div>
                                                            <h3 class="mag-post-title">
                                                                <a href="{{ $post_cat6->getUrl() }}">{{ $post_cat6->name }}</a>
                                                            </h3>
                                                            @include('frontend.post.post_meta', ['post' => $post_cat6])
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        <section id="ascendoor_news_magazine_category_widget-3"
                                 class="widget ascendoor-widget magazine-category-section">
                            <div class="section-header">
                                <h3 class="section-title">Categories</h3>
                            </div>
                            <div class="magazine-category-wrapper">
                                @foreach($list_category_post as $category_post)
                                    @if($loop->index > 6 && $loop->index < 11)
                                        <div class="category-single">
                                            <img src="{{ $category_post->image ?: asset('images/category_image.webp') }}">
                                            <a href="{{ $category_post->getUrl() }}">
                                                {{ $category_post->name }} <span class="category-dots"></span>
                                                <span class="category-no">
                                                    {{ $loop->index - 6 }}
                                                    <span class="posts">Posts</span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                        <section id="ascendoor_news_magazine_trending_posts_widget-3"
                                 class="widget ascendoor-widget magazine-trending-carousel-section">
                            <div class="section-header">
                                <h3 class="section-title">Trending Posts</h3>
                                <a href="#!" class="mag-view-all-link">
                                    View All </a>
                            </div>
                            <div class="magazine-section-body">
                                <div class="magazine-trending-carousel-section-wrapper style-1">
                                    @foreach($share['list_most_view_post'] as $most_view_post)
                                        <div class="mag-post-single has-image list-design">
                                            <div class="mag-post-img">
                                                <a href="{{ $most_view_post->getUrl() }}">
                                                    <img width="1280" height="853"
                                                         src="{{ $most_view_post->image }}"
                                                         class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                         alt="{{ $most_view_post->name }}" decoding="async"/>
                                                </a>
                                                <span class="trending-counter">{{ $loop->index + 1 }}</span>
                                            </div>
                                            <div class="mag-post-detail">
                                                <div class="mag-post-category">
                                                    @foreach($most_view_post->categories as $cat)
                                                        <a href="{{ $cat->getUrl() }}"
                                                           style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                    @endforeach
                                                </div>
                                                <h3 class="mag-post-title">
                                                    <a href="{{ $most_view_post->getUrl() }}">
                                                        {{ $most_view_post->name }}
                                                    </a>
                                                </h3>
                                                @include('frontend.post.post_meta', ['post' => $most_view_post])
                                                <div class="mag-post-excerpt">
                                                    <p>{{ \App\Libs\Util::limitContentWords($most_view_post->description) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="above-footer-widgets-section ascendoor-widget-area">
            <div class="ascendoor-wrapper">
                @if($list_category_post->has(11))
                    @php($category_post_11 = $list_category_post->get(11))
                    <section id="ascendoor_news_posts_carousel_widget-5"
                             class="widget ascendoor-widget magazine-post-carousel-section">
                        <div class="section-header">
                            <h3 class="section-title">{{ $category_post_11->name }}</h3>
                            <a href="{{ $category_post_11->getUrl() }}" class="mag-view-all-link">
                                View All </a>
                        </div>
                        <div class="magazine-section-body">
                            <div class="magazine-post-carousel-section-wrapper post-carousel magazine-carousel-slider-navigation">
                                @foreach($category_post_11->posts as $post_cat11)
                                    <div class="mag-post-single has-image tile-design">
                                        <div class="mag-post-img">
                                            <a href="{{ $post_cat11->getUrl() }}">
                                                <img width="640" height="427"
                                                     src="{{ $post_cat11->image }}"
                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                     alt="{{ $post_cat11->name }}"
                                                     decoding="async"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category with-background">
                                                @foreach($post_cat11->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h3 class="mag-post-title">
                                                <a href="{{ $post_cat11->getUrl() }}">{{ $post_cat11->name }}</a>
                                            </h3>
                                            @include('frontend.post.post_meta', ['post' => $post_cat11])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="post-carousel_dots">
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                                <div class="slider_navigators-wrraper">
                                    <div class="slider_navigators"></div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
                @if($list_category_post->has(12))
                    @php($category_post_12 = $list_category_post->get(12))
                    <section id="ascendoor_news_magazine_grid_widget-5"
                             class="widget ascendoor-widget magazine-grid-section style-1">
                        <div class="section-header">
                            <h3 class="section-title">{{ $category_post_12->name }}</h3>
                            <a href="{{ $category_post_12->getUrl() }}" class="mag-view-all-link">
                                View All </a>
                        </div>
                        <div class="magazine-section-body">
                            <div class="magazine-grid-section-wrapper">
                                @foreach($category_post_12->posts as $post_cat12)
                                    <div class="mag-post-single has-image">
                                        <div class="mag-post-img">
                                            <a href="{{ $post_cat12->getUrl() }}">
                                                <img width="1280" height="853"
                                                     src="{{ $post_cat12->image }}"
                                                     class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                     alt="{{ $post_cat12->name }}"
                                                     decoding="async"/>
                                            </a>
                                        </div>
                                        <div class="mag-post-detail">
                                            <div class="mag-post-category">
                                                @foreach($post_cat12->categories as $cat)
                                                    <a href="{{ $cat->getUrl() }}"
                                                       style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                                @endforeach
                                            </div>
                                            <h3 class="mag-post-title">
                                                <a href="{{ $post_cat12->getUrl() }}">
                                                    {{ $post_cat12->name }}
                                                </a>
                                            </h3>
                                            @include('frontend.post.post_meta', ['post' => $post_cat12])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>
@endsection

@push('bottom')

@endpush