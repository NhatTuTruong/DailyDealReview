<footer id="colophon" class="site-footer">
    <div class="site-footer-top">
        <div class="ascendoor-wrapper">
            <div class="footer-widgets-wrapper three-column-1">
                <div class="footer-widget-single">
                    <section id="block-8" class="widget widget_block">
                        <h2 class="wp-block-heading">{{ $setting['site_name'] }}</h2>
                    </section>
                    <section id="block-9" class="widget widget_block widget_text">
                        <div>{!! $setting['footer_info'] !!}</div>
                    </section>
                    <section id="ascendoor_news_social_icons-3" class="widget widget_ascendoor_news_social_icons">
                        <div class="menu-social-menu-container">
                            <ul id="menu-social-menu-1" class="social-links">
                                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                    <a href="{{ $setting['instagram'] }}" target="_blank">
                                        <span class="screen-reader-text">Instagram</span>
                                    </a>
                                </li>
                                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                    <a href="{{ $setting['youtube'] }}" target="_blank">
                                        <span class="screen-reader-text">YouTube</span>
                                    </a>
                                </li>
                                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                    <a href="{{ $setting['facebook'] }}" target="_blank">
                                        <span class="screen-reader-text">Facebook</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
                <div class="footer-widget-single">
                    <section id="block-10" class="widget widget_block">
                        <h2 class="wp-block-heading">Gallery</h2>
                    </section>
                    <section id="block-12" class="widget widget_block widget_media_gallery">
                        <figure class="wp-block-gallery has-nested-images columns-default is-cropped wp-block-gallery-1 is-layout-flex wp-block-gallery-is-layout-flex">
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="1024"
                                     height="641" data-id="3893"
                                     src="{{ asset('images/gallery/blossom-plant-photography-flower-petal-daisy-1101297-pxhere.com_.webp') }}"
                                     alt="Gallery1" class="wp-image-3893"/>
                            </figure>
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="1024"
                                     height="682" data-id="3891"
                                     src="{{ asset('images/gallery/nature-forest-woman-flower-smoke-green-125827-pxhere.com_-1024x682.webp') }}"
                                     alt="Gallery2" class="wp-image-3891"/>
                            </figure>
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="1024"
                                     height="682" data-id="3876"
                                     src="{{ asset('images/gallery/music-play-guitar-concert-red-color-1409629-pxhere.com_-1024x682.webp') }}"
                                     alt="Gallery2" class="wp-image-3876"/>
                            </figure>
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="640"
                                     height="427" data-id="3196"
                                     src="{{ asset('images/gallery/pexels-wendy-wei-1190298-min.webp') }}"
                                     alt="Gallery4" class="wp-image-3196"/>
                            </figure>
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="640"
                                     height="960" data-id="3197"
                                     src="{{ asset('images/gallery/pexels-hygor-sakai-2311713-min.webp') }}"
                                     alt="" class="wp-image-3197"/>
                            </figure>
                            <figure class="wp-block-image size-large">
                                <img loading="lazy" decoding="async" width="640"
                                     height="960" data-id="3191"
                                     src="{{ asset('images/gallery/pexels-wendy-wei-1699161-min.webp') }}"
                                     alt="" class="wp-image-3191"/>
                            </figure>
                        </figure>
                    </section>
                </div>
                <div class="footer-widget-single">
                    <section id="ascendoor_news_magazine_small_list_widget-5"
                             class="widget ascendoor-widget magazine-small-list-section style-1">
                        <div class="section-header">
                            <h6 class="widget-title">Recent Viewed</h6>
                        </div>
                        <div class="magazine-section-body">
                            <div class="magazine-list-section-wrapper">
                                @foreach($share['list_most_view_post'] as $most_view_post)
                                    @if($loop->index < 3)
                                        <div class="mag-post-single has-image list-design">
                                            <div class="mag-post-img">
                                                <a href="{{ $most_view_post->getUrl() }}">
                                                    <img width="1279" height="853"
                                                         src="{{ $most_view_post->image }}"
                                                         class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                         alt="{{ $most_view_post->name }}" decoding="async"
                                                         loading="lazy"/>
                                                </a>
                                            </div>
                                            <div class="mag-post-detail">
                                                <div class="mag-post-detail-inner">
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
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <!-- .footer-top -->
    <div class="site-footer-bottom">
        <div class="ascendoor-wrapper">
            <div class="site-footer-bottom-wrapper style-1">
                <div class="site-info">
                    <span>{{ $setting['copyright'] }}</span>
                </div>
                <!-- .site-info -->
                <div class="social-icons">
                    <div class="menu-social-menu-container">
                        <ul id="menu-social-menu-2" class="menu social-links">
                            <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                <a href="{{ $setting['instagram'] }}" target="_blank">
                                    <span class="screen-reader-text">Instagram</span>
                                </a>
                            </li>
                            <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                <a href="{{ $setting['youtube'] }}" target="_blank">
                                    <span class="screen-reader-text">YouTube</span>
                                </a>
                            </li>
                            <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                <a href="{{ $setting['facebook'] }}" target="_blank">
                                    <span class="screen-reader-text">Facebook</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>