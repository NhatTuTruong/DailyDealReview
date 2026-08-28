@extends('frontend.index')

@section('content')

    <section class="position-relative container-codes">
        <div class="container">
            <div class="row pt-5">
                <div class="col-xs-12 col-lg-3 bi">
                    <div class="shadow-sm bp">
                        <div class="logo">
                            <a href="#!" class="js-btn_deal_click"
                               data-url="{{ $store->getUrl() }}?offer={{ $best_offer->id }}"
                               data-affiliate_url="{{ $best_offer->url }}">
                                <img src="{{ $store->image ? asset($store->image) : '/images/store.webp' }}" title="{{ $store->name }} Coupons"
                                     alt="{{ $store->name }} Coupons and Promo Code" onerror="this.onerror=null;this.src='/images/store.webp';">
                            </a>
                        </div>
                        <a class="js-common-log-click go-store js-btn_deal_click"
                           data-click-log-flag="code-top-go-store"
                           data-url="{{ $store->getUrl() }}?offer={{ $best_offer->id }}"
                           data-affiliate_url="{{ $best_offer->url }}"
                           href="#!" rel="nofollow" target="_blank">{{ $store->name }}</a>
                        <div class="vote">
                            <div class="rate-yo jq-ry-container" data-rating="5.0" data-star-width="23px"
                                 data-read-only="1" readonly="readonly" style="width: 115px;">
                                <div class="jq-ry-group-wrapper">
                                    <div class="jq-ry-normal-group jq-ry-group">
                                        @foreach(range(1,5) as $item)
                                            <!--?xml version="1.0" encoding="utf-8"?-->
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 12.705 512 486.59" x="0px" y="0px" xml:space="preserve"
                                                 width="23px" height="23px" fill="gray">
                                                <polygon
                                                        points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 "></polygon>
                                            </svg>
                                        @endforeach
                                    </div>
                                    <div class="jq-ry-rated-group jq-ry-group" style="width: 100%;">
                                        @foreach(range(1,5) as $item)
                                            <!--?xml version="1.0" encoding="utf-8"?-->
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 12.705 512 486.59" x="0px" y="0px" xml:space="preserve"
                                                 width="23px" height="23px" fill="#f39c12">
                                            <polygon
                                                    points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 "></polygon>
                                        </svg>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <p itemprop="ratingValue"> 5.0 </p>
                            <p>&nbsp;&nbsp;/&nbsp;&nbsp;</p>
                            <p itemprop="ratingCount">{{ number_format(min($store->view_num, 100)) }}</p>
                            <p>&nbsp;&nbsp;votes&nbsp;&nbsp;</p>
                            <button class="btn btn-rate js-btn-rate js-common-log-click"
                                    data-click-log-flag="pc_brand_rate_it" data-target="#modal-brand-vote"
                                    data-haslogin="false" data-toggle="modal" data-cookie-key="wish.com-rate"
                                    data-website="wish.com">Rate it
                            </button>
                        </div>
                        <button class="btn btn-outline-primary btn-coupon-group btn-coupon-alert js-common-log-click js-btn_deal_click"
                                data-click-log-flag="pc_brand_get_coupon_alert" data-toggle="modal"
                                data-url="{{ $store->getUrl() }}?offer={{ $best_offer->id }}"
                                data-affiliate_url="{{ $best_offer->url }}"
                                data-target="#modal-newsletter">&nbsp;&nbsp;&nbsp;&nbsp;Get Coupon Alert
                        </button>
                    </div>
                    <div class="container-popular-brand">
                        <div class="shadow-sm brand-coupon-info">
                            <div class="coupon-info">
                                <p class="title">
                                    {{ $offers['total_codes'] }} Coupons, {{ $offers['total_verified'] }} Verified
                                    Coupons
                                </p>
                                <table class="merchant-stats">
                                    <tbody>
                                    <tr>
                                        <td class="merchant-title">Coupon Codes</td>
                                        <td class="merchant-data"> {{ $offers['total_codes'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="merchant-title">Deals</td>
                                        <td class="merchant-data">{{ $offers['total_deals'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="merchant-title">Best Offer</td>
                                        <td class="merchant-data">{{ data_get($offers,'best_offer.offer') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="shop-link"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-9 cl">
                    <h3 class="pb-2">{{ $store->name }} Coupons and Promo Codes</h3>
                    <div class="rich-content-show-hide_bk mb-4" onclick="$(this).toggleClass('more')">
                        {!! $store->description !!}
                    </div>
                    <div class="coupon-filter">
                        <div class="filter-panel">
                            <div class="filter-item js-common-log-click" data-click-log-flag="pc_brand_tab_all"
                                 data-filter-type="all">
                                All({{ count($offers['all']) }})
                            </div>
                            <div class="filter-item js-common-log-click filter-item-click"
                                 data-click-log-flag="pc_brand_tab_verified" data-filter-type="verify">
                                Verified({{ $offers['total_verified'] }})
                            </div>
                            <div class="filter-item js-common-log-click" data-click-log-flag="pc_brand_tab_codes"
                                 data-filter-type="codes">
                                Codes({{ $offers['total_codes'] }})
                            </div>
                            <div class="filter-item js-common-log-click" data-click-log-flag="pc_brand_tab_deals"
                                 data-filter-type="deals">
                                Deals({{ $offers['total_deals'] }})
                            </div>
                        </div>
                    </div>
                    <div class="js-normal">
                        @foreach($offers['all'] as $offer)
                            @if($offer->code)
                                <article
                                        class="shadow-sm rounded cp js-filter js-filter-coupon-type-verify js-filter-coupon-type-codes"
                                        data-brand-flag="all" data-filter-flag="0" data-old-position="1">
                                    <div class="deal">
                                        <div class="deal-info benefit-code">
                                            <div class="discount">
                                                {{ $offer->offer }}
                                            </div>
                                        </div>
                                        <div class="deal-desc" id="cpid-{{ $offer->id }}">
                                            <div class="type-code">
                                                <span>
                                                    @if($offer->verified)
                                                        Verified
                                                    @else
                                                        Unverified
                                                    @endif Code
                                                </span>
                                            </div>
                                            <h2 class="title">
                                                <a href="#!"
                                                   class="js-btn_deal_click"
                                                   data-url="{{ $store->getUrl() }}?offer={{ $offer->id }}"
                                                   data-affiliate_url="{{ $offer->url }}">
                                                    {{ $offer->name }}
                                                </a>
                                            </h2>
                                            <div class="last-click-time-wrap mb-2">
                                                <span class="title">{{ strip_tags($offer->description) }}</span>
                                            </div>
                                            <div class="get-code">
                                                <span>{{ strtoupper($offer->code) }}</span>
                                                <a href="#!" class="js-btn_deal_click"
                                                   data-url="{{ $store->getUrl() }}?offer={{ $offer->id }}"
                                                   data-affiliate_url="{{ $offer->url }}">
                                                    Get Code
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @else
                                <article
                                        class="shadow-sm rounded cp js-filter js-filter-coupon-type-verify js-filter-coupon-type-deals"
                                        data-brand-flag="all" data-filter-flag="0" data-old-position="1">
                                    <div class="deal">
                                        <div class="deal-info benefit-code">
                                            <div class="discount">
                                                {{ $offer->offer }}
                                            </div>
                                        </div>
                                        <div class="deal-desc" id="cpid-{{ $offer->id }}">
                                            <div class="type-deal">
                                                <span>
                                                    @if($offer->verified)
                                                        Verified
                                                    @else
                                                        Unverified
                                                    @endif Deal
                                                </span>
                                            </div>
                                            <h2 class="title">
                                                <a href="#!"
                                                   class="js-btn_deal_click"
                                                   data-url="{{ $store->getUrl() }}?offer={{ $offer->id }}"
                                                   data-affiliate_url="{{ $offer->url }}">
                                                    {{ $offer->name }}
                                                </a>
                                            </h2>
                                            <div class="last-click-time-wrap mb-2">
                                                <span class="title">{!! $offer->description !!}</span>
                                            </div>
                                            <div class="get-deal">
                                                <a href="#!" class="js-btn_deal_click"
                                                   data-url="{{ $store->getUrl() }}?offer={{ $offer->id }}"
                                                   data-affiliate_url="{{ $offer->url }}">
                                                    Get Deal
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>
                    @if($store->about_store)
                        <div class="people-also-ask-container">
                            <h2 class="people-also-ask-title"> About store </h2>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="fix-responsive">{!! $store->about_store !!}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="people-also-ask-container">
                        <h2 class="people-also-ask-title"> How to apply {{ $store->name }} coupon codes</h2>
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    {!! $store->how_to_apply !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="people-also-ask-container">
                        <h2 class="people-also-ask-title">{{ $store->name }} Questions &amp; Answers</h2>
                        {!! $store->faqs !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($offer_modal)
        <div class="modal fade" id="modal-key-coupon" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-key-coupon" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="info">
                            <div class="logo rounded-circle">
                                <img src="{{ $store->image ? asset($store->image) : '/images/store.webp' }}" alt="{{ $store->name }}" onerror="this.onerror=null;this.src='/images/store.webp';">
                            </div>
                            <p class="title">{{ $offer_modal->name }}</p>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($offer_modal->code)
                            <div class="content">
                                <p class="go">Copy the code and go to
                                    <a href="{{ $offer_modal->url }}" class="js-common-log-click"
                                       data-click-log-flag="key-go-store"
                                       rel="nofollow" target="_blank">{{ $store->name }}</a>
                                </p>
                                <div class="input-group mb-3 cp-code">
                                    <input type="text" id="copy-code"
                                           class="form-control"
                                           value="{{ $offer_modal->code }}" readonly="">
                                    <button class="btn btn-primary btn-copy js-btn-copy"
                                            data-clipboard-target="#copy-code"
                                            type="button">Tap To Copy
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="content">
                                <p class="go" style="text-align: center;color: rgba(0,0,0,.5);">No code needed</p><a
                                        class="btn btn-primary go-deal js-common-log-click"
                                        data-click-log-flag="key-go-store"
                                        href="{{ $offer_modal->url }}" rel="nofollow" target="_blank">Go
                                    to {{ $store->name }}</a>
                            </div>
                        @endif
                        <a class="brand-more" href="{{ $store->getUrl() }}">More {{ $store->name }} &gt;&gt;</a>
                    </div>
                    <div class="key-pc-footer">
                        <div class="key-text">
                            <img src="{{ $setting['logo'] }}" alt="{{ $setting['site_name'] }}"
                                 style="height: 32px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('bottom')
    <link rel='stylesheet' href='{{ asset('css/deals/bootstrap.min.css') }}?v=1.1.3' media='all'/>
    <link rel='stylesheet' href='{{ asset('css/deals/app.css') }}?v=1.1.3' media='all'/>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}?v=1.1.1"></script>
    <script>
        window.appHelper = {
            blockUIBody: null,
            request: null,
            CopyTexttoClipboard: function (t, e) {
                t.select();
                document.execCommand("copy");
                e()
            },
            renderLink: function (t, e) {
                let o = new URL(window.location.href);
                return o.searchParams.has(t) ? o.searchParams.set(t, e) : o.searchParams.append(t, e), o.toString()
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            $(".filter-item").each(function () {
                let t = $(this).data("filter-type");
                $(this).click(function () {
                    $(".filter-item").removeClass("filter-item-click"), $(this).addClass("filter-item-click"), "all" != t ? $(".js-filter").each(function () {
                        $(this).hasClass("js-filter-coupon-type-" + t) ? $(this).show() : $(this).hide()
                    }) : $(".js-filter").each(function () {
                        $(this).show()
                    })
                });
            })

            document.getElementById("modal-key-coupon") && $("#modal-key-coupon").modal("show"), $(document).ready(function () {
                $(".lazyloaded").each(function () {
                    window.lzld(this)
                })
            })

            $("body").on("click", ".js-btn-copy", function () {
                let t = $(this),
                    e = t.data("clipboard-target");
                window.appHelper.CopyTexttoClipboard($(e), function () {
                    console.log("copy to clipboard : " + $(e).val()), t.text("Copied !")
                })
            })
        });

        function handleClickOffer(event) {
            event.preventDefault();
            const affiliateUrl = this.getAttribute('data-affiliate_url');
            const offerUrl = this.getAttribute('data-url');
            window.open(offerUrl, '_blank');
            window.location.href = affiliateUrl;
        }

        const btnOfferUrl = document.querySelectorAll('.js-btn_deal_click');
        btnOfferUrl.forEach(function (elm) {
            elm.addEventListener('click', handleClickOffer);
        })

        document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("hasClickedOnce")) {
                const handleClick = function (event) {

                    let affiliateUrl = "{{ $best_offer->url }}";
                    let offerUrl = "{{ $store->getUrl() }}?offer={{ $best_offer->id }}";
                    window.open(offerUrl, '_blank');
                    window.location.href = affiliateUrl;

                    localStorage.setItem("hasClickedOnce", "true");
                    document.removeEventListener("click", handleClick);
                };

                document.addEventListener("click", handleClick);
            }
        });

    </script>
@endpush