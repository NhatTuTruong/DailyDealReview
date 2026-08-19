<div dusk="coupon-card" class="tw-cursor-auto">
    <div class="tw-rounded-sm tw-shadow tw-bg-white sm:tw-p-4 tw-p-2 sm:tw-mb-4 tw-mb-2 xl:tw-h-full">
        <div class="tw-flex lato tw-h-full">
            @if(!empty($show_logo) && $coupon->store)
                <div class="tw-text-center xl:tw-w-1/6 tw-w-1/4 xl:tw-min-w-1/6 tw-min-w-1/4">
                    <a href="{{ $coupon->store->getUrl() }}?coupon={{ $coupon->id }}"
                       title="{{ $coupon->store->name }} Coupons and Promo Codes"
                       class="card-logo tw-flex tw-justify-center tw-items-center tw-h-full">
                        <img src="{{ $coupon->store->image }}"
                             alt="{{ $coupon->store->name }} Coupons and Promo Codes"
                             width="180"
                             height="110" loading="lazy"
                             class="sized-img">
                    </a>
                </div>
            @else
                <div class="tw-text-center xl:tw-w-1/6 tw-w-1/4 xl:tw-min-w-1/6 tw-min-w-1/4">
                    <a href="{{ $coupon->store->getUrl() }}?coupon={{ $coupon->id }}" target="_blank">
                        <div class="tw-flex tw-flex-col tw-justify-around tw-text-center tw-text-orange tw-border-2 tw-border-orange-light tw-rounded tw-h-full tw-py-0 tw-px-1">
                            <div class="tw-border-b-2 tw-border-orange-lighter sm:tw-py-3 tw-py-1">
                                <div class="roboto-slab tw-font-bold tw-leading-tight tw-my-0">
                                <span class="tw-inline-block cdroboto-slab tw-text-orange sm:tw-text-4xl tw-text-4xl">
                                    {{ $coupon->value }}
                            </span> <br>
                                    <span class="sm:tw-block tw-hidden">{{ $coupon->type ?: 'Off' }}</span>
                                </div>
                            </div>
                            <p class="sm:tw-text-xl tw-text-lg tw-font-light roboto-slab sm:tw-py-3 tw-py-1">Hot!</p>
                        </div>
                    </a>
                </div>
            @endif
            <div class=" tw-flex tw-leading-tight tw-w-5/6 sm:tw-ml-6 tw-ml-4">
                <div class="tw-flex-grow tw-pr-4 coupon-card-inner-container">
                    <a id="testingId3" href="#!"
                       rel="nofollow"
                       class="tw-text-grey-darker sm:tw-text-xl tw-text-lg tw-font-medium hover:tw-text-blue lato js-btn_deal_click"
                       data-url="{{ $coupon->store ? ($coupon->store->getUrl() . '?coupon=' . $coupon->id) : $coupon->url }}"
                       data-affiliate_url="{{ $coupon->url }}">
                        {{ $coupon->name }}
                    </a>
                    <div class="sm:tw-leading-loose tw-leading-normal">
                        <p class="tw-text-grey sm:tw-text-base tw-text-xs lato">Ongoing Offer</p>
                        @if($coupon->is_verified)
                            <span class="tw-text-green sm:tw-border sm:tw-border-green sm:tw-text-green sm:tw-text-base tw-text-xs lato sm:tw-rounded sm:tw-px-2 sm:tw-py-1">Verified</span>
                        @endif
                        @if($coupon->is_featured)
                            <span class="tw-text-orange  sm:tw-border sm:tw-border-orange  sm:tw-text-orange  sm:tw-text-base tw-text-xs lato sm:tw-rounded sm:tw-px-2 sm:tw-py-1">Featured</span>
                        @endif
                        <div class="tw-relative tw-hidden lg:tw-block tw-text-grey-darker tw-font-light tw-cursor-pointer">
                            <span class="js-btn-toggle-detail">Details: <i class="fa-caret-right fa"></i></span>
                            <p class="tw-absolute tw-border tw-rounded-sm tw-bg-white tw-text-sm tw-py-2 tw-px-4 tw-w-64 tw-z-20 hidden js-toggle-detail">
                                {{ strip_tags($coupon->description) }}</p>
                        </div>
                    </div>
                </div>
                <div class="tw-hidden md:tw-flex tw-items-start tw-whitespace-no-wrap tw-min-w-1/4">
                    <div rel="nofollow"
                         class="tw-relative tw-rounded-sm tw-overflow-hidden tw-cursor-pointer tw-w-full lato">
                        @if($coupon->code)
                            <div class="tw-bg-grey-lighter tw-border tw-text-grey-dark tw-text-right tw-w-full tw-p-2 ">
                                {{ $coupon->code }}
                            </div>
                            <div class="tw-absolute tw-pin-t tw-pin-l tw-bg-blue hover:tw-bg-blue-dark tw-border tw-border-blue tw-text-center tw-text-white tw-min-w-5/6 tw-p-2 js-btn_deal_click"
                                 data-url="{{ $coupon->store ? ($coupon->store->getUrl() . '?coupon=' . $coupon->id) : $coupon->url }}"
                                 data-affiliate_url="{{ $coupon->url }}">
                                Get Code
                            </div>
                        @else
                            <div class="tw-bg-blue hover:tw-bg-blue-dark tw-text-center tw-text-white tw-py-2 tw-px-4 js-btn_deal_click"
                                 data-url="{{ $coupon->store ? ($coupon->store->getUrl() . '?coupon=' . $coupon->id) : $coupon->url }}"
                                 data-affiliate_url="{{ $coupon->url }}">
                                Get Offer
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

