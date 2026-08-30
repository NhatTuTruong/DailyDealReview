@extends('frontend.index')

@section('content')
    <div id="content" class="site-content deal-page">
        <div class="ascendoor-wrapper">
            <div class="ascendoor-page">
                <main id="primary" class="site-main">
                    <nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs">
                        <ul class="trail-items">
                            <li class="trail-item trail-begin">
                                <a href="{{ route('home_page') }}" rel="home"><span>Home</span></a>
                            </li>
                            <li class="trail-item trail-end">
                                <span><span>{{ $pageTitle }}</span></span>
                            </li>
                        </ul>
                    </nav>

                    <header class="deal-page-header">
                        <h1 class="page-title">{{ $pageTitle }}</h1>
                        <p class="deal-page-intro">
                            Featured coupon codes and promotions from popular brands. More deals coming soon.
                        </p>
                    </header>

                    <div class="deal-grid">
                        @foreach($deals as $deal)
                            <article class="deal-card">
                                <div class="deal-card-brand">
                                    <a href="{{ $deal['store_url'] }}" class="deal-card-logo" title="{{ $deal['brand'] }}">
                                        <img src="{{ asset($deal['image']) }}"
                                             alt="{{ $deal['brand'] }}"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null;this.src='{{ asset('images/store.webp') }}';"/>
                                    </a>
                                    <div class="deal-card-brand-meta">
                                        <span class="deal-card-badge">{{ $deal['badge'] }}</span>
                                        <h2 class="deal-card-brand-name">
                                            <a href="{{ $deal['store_url'] }}">{{ $deal['brand'] }}</a>
                                        </h2>
                                    </div>
                                </div>

                                <div class="deal-card-body">
                                    <h3 class="deal-card-title">{{ $deal['title'] }}</h3>
                                    <p class="deal-card-description">{{ $deal['description'] }}</p>

                                    @if(!empty($deal['code']))
                                        <div class="deal-card-code-wrap">
                                            <span class="deal-card-code-label">Code</span>
                                            <button type="button"
                                                    class="deal-card-code js-deal-copy-code"
                                                    data-code="{{ $deal['code'] }}"
                                                    aria-label="Copy coupon code {{ $deal['code'] }}">
                                                {{ $deal['code'] }}
                                            </button>
                                        </div>
                                    @endif

                                    <a href="{{ $deal['cta_url'] }}"
                                       class="deal-card-cta"
                                       target="_blank"
                                       rel="nofollow noopener">
                                        Get Deal
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection

@push('bottom')
    <script>
        document.querySelectorAll('.js-deal-copy-code').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var code = btn.getAttribute('data-code') || '';
                if (!code) {
                    return;
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(function () {
                        btn.classList.add('is-copied');
                        btn.textContent = 'Copied!';
                        setTimeout(function () {
                            btn.classList.remove('is-copied');
                            btn.textContent = code;
                        }, 1500);
                    });
                    return;
                }

                window.prompt('Copy coupon code:', code);
            });
        });
    </script>
@endpush
