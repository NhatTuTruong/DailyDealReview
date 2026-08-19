<form action="#!" method="POST"
      class="tw-flex tw-flex-col tw-w-full sm:tw-w-3/4">
    <div class="tw-flex">
        <input name="email" type="text"
               placeholder="Enter Your Email..."
               class="tw-flex-grow tw-rounded-l-sm tw-text-lg tw-text-grey-darker tw-min-w-0 tw-p-4">
        <button dusk="newsletter-submit" type="button"
                class="tw-rounded-r-sm tw-text-grey-lightest tw-bg-grey-darkest tw-px-4 tw-py-2 js_btn_subscribe"
                data-url="{{ route('subscribe') }}">Sign Up
        </button>
    </div>

    <span class="tw-text-xs tw-mt-1 js_response"></span>
    <a href="#!" class="tw-self-end tw-text-xs tw-text-white tw-mt-1">By Signing Up, you
        agree to our terms of service</a>
</form>