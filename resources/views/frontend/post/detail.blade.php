@extends('frontend.index')

@section('content')

    <div id="content" class="site-content">
        <div class="ascendoor-wrapper">
            <div class="ascendoor-page">
                <main id="primary" class="site-main">
                    <article id="post-{{ $post->id }}"
                             class="post-{{ $post->id }} post type-post status-publish format-standard has-post-thumbnail hentry category-finance category-world-politics tag-health">
                        <div class="mag-post-single">
                            <div class="mag-post-detail">
                                <div class="mag-post-category">
                                    @foreach($post->categories as $cat)
                                        <a href="{{ $cat->getUrl() }}"
                                           style="--cat-color: {{ $cat->getColorLabel() }};">{{ $cat->name }}</a>
                                    @endforeach
                                </div>
                                <header class="entry-header">
                                    <h1 class="entry-title">{{ $post->name }}</h1>
                                    <div class="mag-post-meta">
                                        <span class="post-author">
                                            <a class="url fn n" href="#!">
                                                <i class="fas fa-user"></i>{{ $setting['author_name'] ?? 'Admin' }}</a>
                                        </span>
                                                        <span class="post-date">
                                            <a href="#!" rel="bookmark">
                                                <i class="far fa-clock"></i>
                                                <time class="entry-date published">{{ $post->created_at->format('F d, Y') }}</time>
                                            </a>
                                        </span>
                                    </div>
                                </header><!-- .entry-header -->
                            </div>
                        </div>
                        <div class="post-thumbnail">
                            <img width="1280" height="853"
                                 src="{{ $post->image }}"
                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                 alt="{{ $post->name }}"
                                 decoding="async" fetchpriority="high"/>
                        </div>
                        <div class="entry-content fix-responsive">
                            {!! $post->content !!}
                        </div><!-- .entry-content -->
                        @if(!empty($post->getTags()))
                            <footer class="entry-footer">
            <span class="tags-links">Tagged
                @foreach($post->getTags() as $post_tag)
                    <a href="{{ route('post_tag', \Illuminate\Support\Str::slug($post_tag)) }}"
                       rel="tag">{{ $post_tag }}@if(!$loop->last)
                            ,
                        @endif
                </a>
                @endforeach
            </span>
                            </footer>
                        @endif
                    </article>
                    <div class="related-posts">
                        <h2>Related Posts</h2>
                        <div class="row">
                            @foreach($other_posts as $other_post)
                                <div>
                                    <article id="post-{{ $other_post->id }}"
                                             class="post-{{ $other_post->id }} post type-post status-publish format-standard has-post-thumbnail hentry category-finance tag-health">
                                        <div class="post-thumbnail">
                                            <img width="1280" height="853"
                                                 src="{{ $other_post->image }}"
                                                 class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                 alt="{{ $other_post->name }}"
                                                 decoding="async" loading="lazy"/>
                                        </div>
                                        <header class="entry-header">
                                            <h5 class="entry-title">
                                                <a href="{{ $other_post->getUrl() }}"
                                                   rel="bookmark">{{ $other_post->name }}</a>
                                            </h5>
                                        </header>
                                        <div class="entry-content">
                                            <p>{{ \App\Libs\Util::limitContentWords($other_post->description) }}</p>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- #comments -->
                </main>
                <aside id="secondary" class="widget-area ascendoor-widget-area">
                    @include('frontend/post/secondary')
                </aside>
            </div>
        </div>
    </div>

@endsection
