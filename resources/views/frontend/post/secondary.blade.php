<section id="block-2" class="widget widget_block widget_search">
    <form role="search" method="get" action="#!"
          class="wp-block-search__button-outside wp-block-search__text-button wp-block-search">
        <label class="wp-block-search__label" for="wp-block-search__input-1">Search</label>
        <div class="wp-block-search__inside-wrapper ">
            <input class="wp-block-search__input js-search-auto-completed"
                   id="wp-block-search__input-1"
                   placeholder="" value="" type="search"
                   name="s" required autocomplete="off"/>
            <button aria-label="Search" class="wp-block-search__button wp-element-button"
                    type="button">Search
            </button>
        </div>
    </form>
    <div class="search_results"></div>
</section>
<section id="block-3" class="widget widget_block">
    <div class="wp-block-group">
        <div class="wp-block-group__inner-container is-layout-flow wp-block-group-is-layout-flow">
            <h2 class="wp-block-heading">Recent Posts</h2>
            <ul class="wp-block-latest-posts__list wp-block-latest-posts">
                @foreach($list_latest_post as $latest_post)
                    <li>
                        <a class="wp-block-latest-posts__post-title"
                           href="{{ $latest_post->getUrl() }}">{{ $latest_post->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
<section id="block-6" class="widget widget_block">
    <div class="wp-block-group">
        <div class="wp-block-group__inner-container is-layout-flow wp-block-group-is-layout-flow">
            <h2 class="wp-block-heading">Categories</h2>
            <ul class="wp-block-categories-list wp-block-categories">
                @foreach($list_category as $item_category)
                    <li class="cat-item cat-item-12">
                        <a href="{{ $item_category['href'] }}">{{ $item_category['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>