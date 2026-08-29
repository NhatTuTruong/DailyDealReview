@php
    $stackPosts = ($share['banner_blog_stack'] ?? collect())->take(3);
@endphp

@if(request()->routeIs('home_page') && $stackPosts->isNotEmpty())
    <div class="banner-blog-stack" aria-label="Featured blog posts">
        @foreach($stackPosts as $post)
            @php
                $cardClass = 'banner-blog-card-' . ($loop->iteration);
                $isFeatured = $loop->last && $stackPosts->count() === 3;
            @endphp
            <a href="{{ $post->getUrl() }}"
               class="banner-blog-card {{ $cardClass }}{{ $isFeatured ? ' is-featured' : '' }}"
               title="{{ $post->name }}">
                <div class="banner-blog-card-image">
                    <img src="{{ $post->image }}"
                         alt="{{ $post->name }}"
                         loading="lazy"
                         decoding="async"/>
                </div>
                <div class="banner-blog-card-body">
                    <span class="banner-blog-category">
                        {{ $post->categories->first()->name ?? 'Blog' }}
                    </span>
                    <span class="banner-blog-title">{{ $post->name }}</span>
                </div>
            </a>
        @endforeach
    </div>
@endif
