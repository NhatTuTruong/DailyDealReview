<!doctype html>
<html class="{{ app()->getLocale() }}" lang="{{ app()->getLocale() }}">
<head>
    {!! $setting['tracking_code_head'] ?? '' !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('vendor/slick/slick.min.css') }}?v=1.1.3" media="all"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.2.2" media="all"/>
    <link rel="preload" as="image" href="{{ asset('images/top-banner.webp') }}" fetchpriority="high">
    @if(!empty($setting['facebook_app_id']))
        <link rel="dns-prefetch" href="https://connect.facebook.net">
    @endif
    <style id="layout-critical-css">
        .mag-post-single .mag-post-img {
            position: relative;
            overflow: hidden;
            background-color: #ececec;
        }

        .mag-post-single .mag-post-img > a {
            display: block;
            aspect-ratio: 10 / 7;
            line-height: 0;
        }

        .mag-post-single .mag-post-img > a img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mag-post-single.list-design {
            display: flex;
            gap: 20px;
        }

        .mag-post-single.list-design .mag-post-img {
            width: 30%;
            flex-shrink: 0;
        }

        .mag-post-single.list-design .mag-post-img > a {
            aspect-ratio: 1 / 1;
        }

        body.home .banner-section.banner-section-style-2 .banner-section-wrapper {
            display: flex;
            flex-wrap: wrap;
            margin-inline: -10px;
        }

        body.home .banner-section.banner-section-style-2 .slider-part {
            width: 60%;
            padding-inline: 10px;
        }

        body.home .banner-section.banner-section-style-2 .editors-pick-part {
            width: 40%;
            padding-inline: 10px;
        }

        body.home .banner-section.banner-section-style-2 .editors-pick-wrapper {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        body.home .banner-grid-single {
            min-height: 400px;
        }

        body.home .editors-pick-part .mag-post-img > a {
            aspect-ratio: 2 / 1;
        }

        body.home .flash-img {
            display: inline-flex;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            overflow: hidden;
        }

        body.home .flash-img img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        body.home .banner-slider:not(.slick-initialized),
        body.home .post-carousel:not(.slick-initialized) {
            overflow: hidden;
        }

        body.home .banner-slider:not(.slick-initialized) > :not(:first-child),
        body.home .post-carousel:not(.slick-initialized) > :not(:first-child) {
            display: none;
        }

        body.home .banner-slider:not(.slick-initialized) {
            min-height: 400px;
        }

        .mag-post-single.tile-design {
            position: relative;
            overflow: hidden;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .mag-post-single.tile-design .mag-post-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .mag-post-single.tile-design .mag-post-img > a {
            height: 100%;
            aspect-ratio: unset;
        }

        .mag-post-single.tile-design .mag-post-detail {
            position: relative;
            z-index: 1;
        }

        body.home .main-widget-section-wrap {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 30px;
        }

        body.home .magazine-grid-section.style-1 .magazine-grid-section-wrapper,
        body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper,
        body.home .magazine-double-category-section .magazine-double-category-section-wrapper {
            display: grid;
            gap: 30px;
        }

        body.home .magazine-grid-section.style-1 .magazine-grid-section-wrapper {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper .mag-post-single {
            grid-column: span 2;
        }

        body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper .mag-post-single:first-child {
            grid-row: span 3;
            grid-column: span 2;
        }

        body.home .magazine-double-category-section .magazine-double-category-section-wrapper {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        body.home .magazine-list-section.style-1 .magazine-list-section-wrapper .mag-post-single.list-design {
            min-height: 300px;
        }

        body.home .magazine-list-section.style-1 .magazine-list-section-wrapper .mag-post-single.list-design .mag-post-img {
            width: 50%;
        }

        @media (min-width: 992px) {
            body.home .main-widget-section-wrap .primary-widgets-section {
                width: calc(70% - 15px);
            }

            body.home .main-widget-section-wrap .secondary-widgets-section {
                width: calc(30% - 15px);
            }
        }

        @media (max-width: 991px) {
            body.home .banner-section.banner-section-style-2 .slider-part,
            body.home .banner-section.banner-section-style-2 .editors-pick-part {
                width: 100%;
            }

            body.home .banner-section.banner-section-style-2 .banner-section-wrapper {
                flex-direction: column;
                gap: 20px;
            }

            body.home .banner-grid-single {
                min-height: 350px;
            }

            body.home .magazine-grid-section.style-1 .magazine-grid-section-wrapper {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            body.home .magazine-double-category-section .magazine-double-category-section-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            body.home .banner-section.banner-section-style-2 .editors-pick-wrapper {
                grid-template-columns: 1fr;
            }

            body.home .magazine-grid-section.style-1 .magazine-grid-section-wrapper,
            body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper {
                grid-template-columns: 1fr;
            }

            body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper .mag-post-single,
            body.home .magazine-tile-list-section.style-1 .magazine-tile-list-section-wrapper .mag-post-single:first-child {
                grid-column: span 1;
                grid-row: span 1;
            }
        }
    </style>

    {!! $setting['meta_tag'] ?? '' !!}

    <title>{{ $setting['meta_title'] }}</title>
    <meta name="keywords" content="{{ $setting['meta_keywords'] }}">
    <meta name="description" content="{{ $setting['meta_description'] }}">
    @if(!empty($setting['noindex']))
        <meta name="robots" content="noindex"/>
    @else
        <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>
    @endif
    <link rel="canonical" href="{{ url()->current() }}"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ $setting['meta_title'] }}"/>
    <meta property="og:description" content="{{ $setting['meta_description'] }}"/>
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:site_name" content="{{ $setting['site_name'] }}"/>
    @if(!empty($setting['og_image']))
        <meta property="og:image" content="{{ url($setting['og_image']) }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="{{ $setting['meta_title'] }}"/>
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:site" content="{{ $setting['site_name'] }}">
    @if(!empty($setting['og_image']))
        <meta name="twitter:image" content="{{ url($setting['og_image']) }}">
    @endif
    <meta name="twitter:description" content="{{ $setting['meta_description'] }}"/>

    <link rel='stylesheet' href='{{ asset('css/block-library/style.min.css') }}?v=1.1.3' media='print' onload="this.media='all'"/>
    <noscript><link rel='stylesheet' href='{{ asset('css/block-library/style.min.css') }}?v=1.1.3'/></noscript>

    <style id='global-styles-inline-css'>
        :root {
            --wp--preset--aspect-ratio--square: 1;
            --wp--preset--aspect-ratio--4-3: 4/3;
            --wp--preset--aspect-ratio--3-4: 3/4;
            --wp--preset--aspect-ratio--3-2: 3/2;
            --wp--preset--aspect-ratio--2-3: 2/3;
            --wp--preset--aspect-ratio--16-9: 16/9;
            --wp--preset--aspect-ratio--9-16: 9/16;
            --wp--preset--color--black: #000000;
            --wp--preset--color--cyan-bluish-gray: #abb8c3;
            --wp--preset--color--white: #ffffff;
            --wp--preset--color--pale-pink: #f78da7;
            --wp--preset--color--vivid-red: #198754;
            --wp--preset--color--luminous-vivid-orange: #ff6900;
            --wp--preset--color--luminous-vivid-amber: #fcb900;
            --wp--preset--color--light-green-cyan: #7bdcb5;
            --wp--preset--color--vivid-green-cyan: #00d084;
            --wp--preset--color--pale-cyan-blue: #8ed1fc;
            --wp--preset--color--vivid-cyan-blue: #0693e3;
            --wp--preset--color--vivid-purple: #9b51e0;
            --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgba(6, 147, 227, 1) 0%, rgb(155, 81, 224) 100%);
            --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
            --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgba(252, 185, 0, 1) 0%, rgba(255, 105, 0, 1) 100%);
            --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgba(255, 105, 0, 1) 0%, rgb(207, 46, 46) 100%);
            --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
            --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
            --wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
            --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
            --wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
            --wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
            --wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
            --wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
            --wp--preset--font-size--small: 13px;
            --wp--preset--font-size--medium: 20px;
            --wp--preset--font-size--large: 36px;
            --wp--preset--font-size--x-large: 42px;
            --wp--preset--spacing--20: 0.44rem;
            --wp--preset--spacing--30: 0.67rem;
            --wp--preset--spacing--40: 1rem;
            --wp--preset--spacing--50: 1.5rem;
            --wp--preset--spacing--60: 2.25rem;
            --wp--preset--spacing--70: 3.38rem;
            --wp--preset--spacing--80: 5.06rem;
            --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
            --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
            --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
            --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);
            --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
        }

        :where(.is-layout-flex) {
            gap: 0.5em;
        }

        :where(.is-layout-grid) {
            gap: 0.5em;
        }

        body .is-layout-flex {
            display: flex;
        }

        .is-layout-flex {
            flex-wrap: wrap;
            align-items: center;
        }

        .is-layout-flex > :is(*, div) {
            margin: 0;
        }

        body .is-layout-grid {
            display: grid;
        }

        .is-layout-grid > :is(*, div) {
            margin: 0;
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em;
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em;
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em;
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em;
        }

        .has-black-color {
            color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-color {
            color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-color {
            color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-color {
            color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-color {
            color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-color {
            color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-color {
            color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-color {
            color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-color {
            color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-color {
            color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-color {
            color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-color {
            color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-black-background-color {
            background-color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-background-color {
            background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-background-color {
            background-color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-background-color {
            background-color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-background-color {
            background-color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-background-color {
            background-color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-background-color {
            background-color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-background-color {
            background-color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-background-color {
            background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-background-color {
            background-color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-black-border-color {
            border-color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-border-color {
            border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-border-color {
            border-color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-border-color {
            border-color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-border-color {
            border-color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-border-color {
            border-color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-border-color {
            border-color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-border-color {
            border-color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-border-color {
            border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-border-color {
            border-color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
            background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;
        }

        .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
            background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;
        }

        .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-orange-to-vivid-red-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;
        }

        .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
            background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;
        }

        .has-cool-to-warm-spectrum-gradient-background {
            background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;
        }

        .has-blush-light-purple-gradient-background {
            background: var(--wp--preset--gradient--blush-light-purple) !important;
        }

        .has-blush-bordeaux-gradient-background {
            background: var(--wp--preset--gradient--blush-bordeaux) !important;
        }

        .has-luminous-dusk-gradient-background {
            background: var(--wp--preset--gradient--luminous-dusk) !important;
        }

        .has-pale-ocean-gradient-background {
            background: var(--wp--preset--gradient--pale-ocean) !important;
        }

        .has-electric-grass-gradient-background {
            background: var(--wp--preset--gradient--electric-grass) !important;
        }

        .has-midnight-gradient-background {
            background: var(--wp--preset--gradient--midnight) !important;
        }

        .has-small-font-size {
            font-size: var(--wp--preset--font-size--small) !important;
        }

        .has-medium-font-size {
            font-size: var(--wp--preset--font-size--medium) !important;
        }

        .has-large-font-size {
            font-size: var(--wp--preset--font-size--large) !important;
        }

        .has-x-large-font-size {
            font-size: var(--wp--preset--font-size--x-large) !important;
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em;
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em;
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em;
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em;
        }

        :root :where(.wp-block-pullquote) {
            font-size: 1.5em;
            line-height: 1.6;
        }
    </style>
    <link rel='stylesheet' href='{{ asset('vendor/fontawesome/css/all.min.css') }}?v=1.1.3' media='print' onload="this.media='all'"/>
    <noscript><link rel='stylesheet' href='{{ asset('vendor/fontawesome/css/all.min.css') }}?v=1.1.3'/></noscript>
    <link rel='stylesheet' href='{{ asset('fonts/db0864cd1418c3620093312f4151b732.css') }}' media='all'/>
    <style id='ascendoor-news-style-inline-css'>
        /* Color */

        :root {
            --header-text-color: #ffffff;
        }

        /* Typograhpy */

        :root {
            --font-heading: "Roboto", serif;
            --font-main: -apple-system, BlinkMacSystemFont, "Roboto", "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }

        body,
        button,
        input,
        select,
        optgroup,
        textarea {
            font-family: "Roboto", serif;
        }

        .site-title a {
            font-family: "Roboto", serif;
        }

        .site-description {
            font-family: "Poppins", serif;
        }
    </style>
    <!-- Styles-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}?v=1.1.3"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ai-post.css') }}?v=1.0.0"/>
    @stack('head')

    <script>var baseUrl = "{{ url('/') }}";var current_locale = "{{ app()->getLocale() }}";</script>
</head>
<body class="{{ trim($setting['body_class'] ?? '') ?: 'home page-template-default page' }} wp-embed-responsive right-sidebar modern-design">
{!! $setting['tracking_code_body'] ?? '' !!}
<div id="page" class="site ascendoor-site-wrapper">
{{--    <a class="skip-link screen-reader-text" href="#primary">Skip to content</a>--}}
{{--    <div id="loader">--}}
{{--        <div class="loader-container">--}}
{{--            <div id="preloader" class="style-2">--}}
{{--                <div class="dot"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    @include('frontend.header')

    @yield('content')

</div>

@include('frontend.footer')

<a href="#" id="scroll-to-top" class="magazine-scroll-to-top all-device">
    <i class="fas fa-chevron-up"></i>
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
        </svg>
    </div>
</a>
<style id='core-block-supports-inline-css'>
    .wp-block-gallery.wp-block-gallery-1 {
        --wp--style--unstable-gallery-gap: var(--wp--style--gallery-gap-default, var(--gallery-block--gutter-size, var(--wp--style--block-gap, 0.5em)));
        gap: var(--wp--style--gallery-gap-default, var(--gallery-block--gutter-size, var(--wp--style--block-gap, 0.5em)));
    }
</style>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}?ver=1.1.3"></script>
<script src="{{ asset('js/navigation.min.js') }}?v=1.1.3" defer></script>
<script src="{{ asset('vendor/slick/slick.min.js') }}?ver=1.1.3" defer></script>
<script src="{{ asset('js/jquery.marquee.min.js') }}?ver=1.1.3" defer></script>
<script src="{{ asset('js/custom.min.js') }}?v=1.1.3" defer></script>
<script src="{{ asset('js/global.js') }}?v=1.1.3" defer></script>
{{--<script src="{{ asset('js/tracking.js') }}?v=1.1.3"></script>--}}

@stack('bottom')

@if(!empty($setting['facebook_app_id']))
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v19.0&appId={{ $setting['facebook_app_id'] }}"
        nonce="uWFE6azL"></script>
@endif
{!! $setting['tracking_code_bottom'] ?? '' !!}
</body>
</html>
