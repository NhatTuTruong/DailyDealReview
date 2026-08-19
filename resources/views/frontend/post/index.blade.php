@extends('frontend.index')

@section('content')

    <div id="content" class="site-content">
        <div class="ascendoor-wrapper">
            <div class="ascendoor-page">
                <main id="primary" class="site-main">
                    @if(empty($tag) && !empty($category))
                        <nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs">
                            <ul class="trail-items">
                                <li class="trail-item trail-begin">
                                    <a href="{{ route('home_page') }}"
                                       rel="home"><span>Home</span>
                                    </a>
                                </li>
                                <li class="trail-item trail-end">
                                <span>
                                    <span>{{ $category->name }}</span>
                                </span>
                                </li>
                            </ul>
                        </nav>
                    @endif
                    <header>
                        <h1 class="page-title">{{ !empty($tag) ? 'Tag: ' . \Illuminate\Support\Str::headline($tag) : (!empty($category) ? $category->name : 'All Posts') }}</h1>
                    </header>
                    <div class="magazine-archive-layout grid-layout grid-column-3">
                        @foreach($posts as $post)
                            <article id="post-{{ $post->id }}"
                                     class="post-{{ $post->id }} post type-post status-publish format-standard has-post-thumbnail hentry">
                                <div class="mag-post-single">
                                    <div class="mag-post-img">
                                        <a class="post-thumbnail" href="{{ $post->getUrl() }}" aria-hidden="true">
                                            <img width="1280" height="853" src="{{ $post->image }}"
                                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                 alt="{{ $post->name }}"
                                                 decoding="async" fetchpriority="high"/>
                                        </a>
                                    </div>
                                    <div class="mag-post-detail">
                                        <div class="mag-post-category">
                                            @foreach($post->categories as $cat)
                                                <a href="{{ $cat->getUrl() }}"
                                                   style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                            @endforeach
                                        </div>
                                        <h2 class="entry-title mag-post-title">
                                            <a href="{{ $post->getUrl() }}"
                                               rel="bookmark">{{ $post->name }}</a>
                                        </h2>
                                        <div class="mag-post-meta">
                                            <span class="post-author">
                                                <a class="url fn n"
                                                   href="#!">
                                                    <i class="fas fa-user"></i>
                                                    {{ $setting['author_name'] ?? 'Admin' }}</a>
                                            </span>
                                            <span class="post-date">
                                                <a href="#!" rel="bookmark">
                                                    <i class="far fa-clock"></i>
                                                    <time class="entry-date published">{{ $post->created_at->format('F d, Y') }}</time>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="mag-post-excerpt">
                                            <p>{{ \App\Libs\Util::limitContentWords($post->description) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <nav class="navigation posts-navigation" aria-label="Posts">
                        <h2 class="screen-reader-text">Posts navigation</h2>
                        {!! $posts->links() !!}
                    </nav>
                </main>
                <aside id="secondary" class="widget-area ascendoor-widget-area">
                    @include('frontend/post/secondary')
                </aside>
            </div>
        </div>
    </div>
@endsection

@push('bottom')
@endpush