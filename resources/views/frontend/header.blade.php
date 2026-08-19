<header id="masthead" class="site-header header-style-3 logo-size-small">
    <div class="top-middle-header-wrapper ascendoor-header-image"
         style="background-image: url({{ asset('images/top-banner.webp') }});">
        <div class="top-header-part">
            <div class="ascendoor-wrapper">
                <div class="top-header-wrapper">
                    <div class="top-header-left">
                        <div class="date-wrap">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ date("D, M d, Y") }}</span>
                        </div>
                        <div class="top-header-menu">
                            <div class="menu-quick-links-container">
                                <ul id="menu-quick-links" class="menu">
                                    @foreach($share['top_menu'] as $top_menu)
                                        <li id="menu-item-{{ $top_menu['id'] }}"
                                            class="menu-item menu-item-type-taxonomy menu-item-object-category">
                                            <a href="{{ $top_menu['href'] }}">{{ $top_menu['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="top-header-right">
                        <div class="ramdom-post">
                            <a href="#!"
                               data-title="View Random Post">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 100 100">
                                    <polyline class="line arrow-end top"
                                              points="5.6,34.2 33.2,34.4 65.6,66.8 93.4,66.3 "></polyline>
                                    <polyline class="line arrow-end bottom"
                                              points="5.6,66.8 33.2,66.6 65.6,34.2 93.4,34.7 "></polyline>
                                    <polyline class="line" points="85.9,24.5 95.4,34.2 86.6,43.5 "></polyline>
                                    <polyline class="line" points="85.9,56.5 95.4,66.2 86.6,75.5 "></polyline>
                                </svg>
                            </a>
                        </div>
                        <div class="social-icons">
                            <div class="menu-social-menu-container">
                                <ul id="menu-social-menu" class="menu social-links">
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
        </div>
        <div class="middle-header-part">
            <div class="ascendoor-wrapper">
                <div class="middle-header-wrapper ">
                    <div class="site-branding site-logo-left">
                        <div class="site-identity">
                            <p class="site-title">
                                <a href="{{ route('home_page') }}" rel="home">{{ $setting['site_name'] }}</a>
                            </p>
                            <p class="site-description">{{ $setting['slogan'] }} </p>
                        </div>
                    </div>
                    <!-- .site-branding -->
                    <div class="mag-adver-part image">
                        <a href="#">
                            <img src="{{ asset('images/ad-area-f.webp') }}"
                                 alt="Banner image">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-header-part-outer">
        <div class="bottom-header-part">
            <div class="ascendoor-wrapper">
                <div class="bottom-header-wrapper">
                    <div class="navigation-part">
                        <nav id="site-navigation" class="main-navigation">
                            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                            <div class="main-navigation-links">
                                <div class="menu-primary-menu-container">
                                    <ul id="menu-primary-menu" class="menu">
                                        @foreach($share['main_menu'] as $main_menu)
                                            @if(!empty($main_menu['children']))
                                                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children">
                                                    <a href="#">{{ $main_menu['name'] }}</a>
                                                    <ul class="sub-menu">
                                                        @foreach($main_menu['children'] as $children)
                                                            <li class="menu-item menu-item-type-custom menu-item-object-custom">
                                                                <a href="{{ $children['href'] }}">{{ $children['name'] }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @else
                                                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                                                    <a href="{{ $main_menu['href'] }}">{{ $main_menu['name'] }}
                                                        @if(strpos($main_menu['name'], 'blog') !== false)
                                                            <span class="menu-description">New</span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </nav>
                        <!-- #site-navigation -->
                    </div>
                    <div class="bottom-header-right-part">
                        <div class="header-search">
                            <div class="header-search-wrap">
                                <a href="#" title="Search" class="header-search-icon">
                                    <i class="fas fa-search"></i>
                                </a>
                                <div class="header-search-form">
                                    <form role="search" method="get" class="search-form"
                                          action="{{ route('search') }}">
                                        <label>
                                            <span class="screen-reader-text">Search for:</span>
                                            <input type="search" class="search-field js-search-auto-completed"
                                                   placeholder="Search &hellip;" autocomplete="off"
                                                   value="{{ $key ?? '' }}" name="key"/>
                                        </label>
                                        <input type="button" class="search-submit" value="Search"/>
                                    </form>
                                    <div class="search_results"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('bottom')
    <script>
        $(document).ready(function () {
            $('.js-search-auto-completed').on('keyup', function () {
                let keyword = $(this).val().trim();

                if (keyword.length < 3) {
                    $('.search_results').html('');
                    return;
                }

                $.ajax({
                    url: '{{ route("search") }}',
                    method: 'POST',
                    data: {keyword: keyword},
                    success: function (response) {
                        $('.search_results').html(response);
                    },
                    error: function () {
                        $('.search_results').html('<p class="text-danger">No results</p>');
                    }
                });
            });
        });
    </script>
@endpush