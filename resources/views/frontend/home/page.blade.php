@extends('frontend.index')

@section('content')

    <div id="content" class="site-content">
        <div class="ascendoor-wrapper">
            <div>
                <main id="primary" class="site-main">
                    <article id="post-{{ $page->id }}"
                             class="post-{{ $page->id }} post type-post status-publish format-standard has-post-thumbnail hentry category-finance category-world-politics tag-health">
                        <div class="mag-post-single">
                            <div class="mag-post-detail">
                                <header class="entry-header">
                                    <h1 class="entry-title">{{ $page->name }}</h1>
                                </header>
                            </div>
                        </div>
                        <div class="entry-content fix-responsive">
                            {!! $page->content !!}
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>

@endsection
