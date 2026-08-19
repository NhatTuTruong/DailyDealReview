@if($coupon_modal)
    <div dusk="merchant-coupon-modal" id="popup_modal" tabindex="-1" role="dialog" class="modal fade in"
         style="display: block;">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" aria-label="Close"
                            class="close js-close_mode_coupon">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="col-md-12 col-sm-12">
                            <a href="{{ $coupon_modal->url }}" target="_blank">
                                <img src="{{ $coupon_modal->store->image }}" style="width:120px"
                                     alt="popup-logo" loading="lazy" class="img-responsive mob-deal-popup-img">
                            </a>
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <p class="deal-popup-text-01">
                                <a href="{{ $coupon_modal->url }}" target="_blank" class="deal-popup-cont">
                                    {{ $coupon_modal->name }}
                                </a>
                            </p>
                        </div>
                    </div>
                    @if($coupon_modal->code)
                        <div class="col-md-12 col-sm-12">
                            <div class="deal-popup-code">
                                <h3 id="copy-target" class="deal-popup-code-str">
                                    {{ $coupon_modal->code }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 text-center">
                            <button type="button" data-clipboard-action="copy"
                                    data-clipboard-target="#copy-target"
                                    class="btn deal-popup-button copy-button">Copy Code
                            </button>
                            <a href="{{ $coupon_modal->url }}" target="_blank" class="deal-popup-cont">Continue to offer
                                <i aria-hidden="true" class="fa fa-chevron-right" style="font-size: 12px;"></i>
                            </a>
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <p class="deal-popup-text-02"><br>
                                Details: Click "Show Coupon Code" To Activate This Deal. Exclusions May Apply
                            </p>
                        </div>
                    @else
                        <div class="col-md-12 col-sm-12 text-center">
                            <a href="{{ $coupon_modal->url }}" target="_blank"
                               class="deal-popup-cont">
                                <button type="button" class="btn deal-popup-button">Continue to offer
                                    <i aria-hidden="true" class="fa fa-chevron-right"
                                       style="font-size: 12px;"></i>
                                </button>
                            </a></div>
                        <div class="col-md-12 col-sm-12">
                            <p class="deal-popup-text-02">
                                <br>Details: No Promo
                                Code Needed. Click "Get Offer" To Activate This Deal. Exclusions May Apply
                            </p>
                        </div>
                    @endif
                    <div class="col-md-12 col-sm-12 text-center coupon-feedback"
                         style="background: rgb(238, 238, 238); padding: 1rem; margin: 1rem 0px;">
                        <div class="feedback-cta">
                            <span class="m-x-1">Did This Offer Work?</span>
                            <span dusk="coupon-feedback-positive" class="m-x-1 text-primary clickable">
                                        <i class="fa fa-thumbs-up"></i>
                                    </span>
                            <span dusk="coupon-feedback-negative" class="m-x-1 text-primary clickable">
                                        <i class="fa fa-thumbs-down"></i>
                                    </span>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12">
                        <div dusk="newsletter-signup" class=" tw-bg-blue tw-p-4"><p
                                    class="tw-text-white newsletter-header"
                                    style="display: block; text-align: center; padding: 1rem; font-size: 1.2rem;">
                                Sign
                                up for our newsletter and never miss another deal from Udemy and other great
                                brands.</p>
                            <form action="#!" method="POST">
                                <div class="tw-flex">
                                    <input name="email" type="text"
                                           placeholder="Enter Your Email..."
                                           class="tw-flex-grow tw-rounded-l-sm tw-text-lg tw-text-grey-darker tw-min-w-0 tw-p-4">
                                    <button dusk="newsletter-submit" type="button"
                                            class="tw-rounded-r-sm tw-text-grey-lightest tw-bg-grey-darkest tw-px-4 tw-py-2 js_btn_subscribe"
                                            data-url="{{ route('subscribe') }}">
                                        Sign Up
                                    </button>
                                </div>
                                <span class="tw-text-xs tw-mt-1 js_response tw-block tw-w-full"></span>
                                <a href="#!" class="tw-text-xs tw-text-white tw-mb-4">
                                    By Signing Up, you agree to our terms of service</a>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 col-md-offset-3 col-sm-12 deal-popup-list">
                        <p class="text-center" style="margin-top: 1.5rem;">
                            Share this deal with your friends and family </p>
                    </div>
                    <div class="col-md-12 col-sm-12" style="margin-top: 2rem;">
                        <div class="text-center">
                            <ul class="list-inline modal-social">
                                <li>
                                    <a href="https://www.facebook.com/sharer.php?t={{ $coupon_modal->name }}&amp;u={{ $coupon_modal->store->getUrl() }}">
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?text={{ $coupon_modal->name }}+-+{{ $coupon_modal->store->getUrl() }}&amp;source=webclient">
                                        <i class="fa fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://plus.google.com/share?url={{ $coupon_modal->store->getUrl() }}">
                                        <i class="fa fa-google-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="visibility: hidden;">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="js-modal_backdrop modal-backdrop fade in"></div>
@endif
