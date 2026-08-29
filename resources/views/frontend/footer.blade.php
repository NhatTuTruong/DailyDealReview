<footer id="colophon" class="site-footer">
    <div class="site-footer-top">
        <div class="ascendoor-wrapper">
            <div class="footer-widgets-wrapper three-column-1">
                <div class="footer-widget-single">
                    <section id="block-8" class="widget widget_block">
                        <h2 class="wp-block-heading">{{ $setting['site_name'] }}</h2>
                        @php
                            $siteDescription = trim(strip_tags($setting['footer_info'] ?? ''))
                                ?: trim($setting['meta_description'] ?? '');
                        @endphp
                        @if($siteDescription !== '')
                            <div class="site-footer-description widget_text">
                                @if(!empty(trim($setting['footer_info'] ?? '')))
                                    {!! $setting['footer_info'] !!}
                                @else
                                    <p>{{ $setting['meta_description'] }}</p>
                                @endif
                            </div>
                        @endif
                    </section>
                    @if(!empty(\App\Models\Setting::getSocialLinks($setting)))
                    <section id="ascendoor_news_social_icons-3" class="widget widget_ascendoor_news_social_icons">
                        <div class="menu-social-menu-container">
                            @include('frontend.blocks.social-links', [
                                'setting' => $setting,
                                'listClass' => 'social-links',
                                'listId' => 'menu-social-menu-1',
                            ])
                        </div>
                    </section>
                    @endif
                </div>
                <div class="footer-widget-single">
                    @if(($share['post_count'] ?? 0) >= 6 && !empty($share['list_hot_post_gallery']) && $share['list_hot_post_gallery']->count() > 0)
                    <section id="block-10" class="widget widget_block">
                        <h2 class="wp-block-heading">Blog Gallery</h2>
                    </section>
                    <section id="block-12" class="widget widget_block widget_media_gallery">
                        <figure class="wp-block-gallery has-nested-images columns-default is-cropped wp-block-gallery-1 is-layout-flex wp-block-gallery-is-layout-flex">
                            @foreach($share['list_hot_post_gallery'] as $hot_post)
                                <figure class="wp-block-image size-large">
                                    <a href="{{ $hot_post->getUrl() }}" title="{{ $hot_post->name }}">
                                        <img loading="lazy" decoding="async"
                                             src="{{ $hot_post->image }}"
                                             alt="{{ $hot_post->name }}"
                                             class="wp-image-{{ $hot_post->id }}"
                                             onerror="this.closest('figure.wp-block-image')?.remove();"/>
                                    </a>
                                </figure>
                            @endforeach
                        </figure>
                    </section>
                    @endif
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
                @if(!empty(\App\Models\Setting::getSocialLinks($setting)))
                <div class="social-icons">
                    <div class="menu-social-menu-container">
                        @include('frontend.blocks.social-links', [
                            'setting' => $setting,
                            'listClass' => 'menu social-links',
                            'listId' => 'menu-social-menu-2',
                        ])
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</footer>