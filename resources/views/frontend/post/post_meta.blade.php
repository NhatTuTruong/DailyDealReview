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