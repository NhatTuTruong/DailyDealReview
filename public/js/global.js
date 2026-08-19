$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
//
// document.addEventListener("DOMContentLoaded", function () {
//     document.querySelectorAll(".js-btn-toggle-detail").forEach((btn) => {
//         btn.addEventListener("click", function () {
//             // Tìm thẻ <i> bên trong button
//             const icon = btn.querySelector("i");
//             if (icon) {
//                 // Toggle class 'fa-caret-right' <-> 'fa-caret-down'
//                 icon.classList.toggle("fa-caret-right");
//                 icon.classList.toggle("fa-caret-down");
//             }
//
//             // Tìm phần tử <p class="js-toggle-detail"> ngay bên cạnh
//             const detailParagraph = btn.nextElementSibling;
//             if (detailParagraph && detailParagraph.classList.contains("js-toggle-detail")) {
//                 detailParagraph.classList.toggle("hidden");
//             }
//         });
//     });
// });
//
// function handleClickOffer(event) {
//     event.preventDefault();
//     const affiliateUrl = this.getAttribute('data-affiliate_url');
//     const offerUrl = this.getAttribute('data-url');
//     window.open(offerUrl, '_blank');
//     window.location.href = affiliateUrl;
// }
//
// const btnOfferUrl = document.querySelectorAll('.js-btn_deal_click');
// btnOfferUrl.forEach(function (elm) {
//     elm.addEventListener('click', handleClickOffer);
// })
//
// //Close model
// document.querySelectorAll('.js-close_mode_coupon').forEach(button => {
//     button.addEventListener('click', function () {
//         // Tìm parent có id=popup_modal và xóa style 'display: block;'
//         let popup = this.closest('#popup_modal');
//         if (popup) {
//             popup.style.removeProperty('display');
//         }
//
//         // Tìm tất cả các phần tử có class 'js-modal_backdrop' và xóa bỏ tất cả class khác
//         document.querySelectorAll('.js-modal_backdrop').forEach(backdrop => {
//             backdrop.className = 'js-modal_backdrop'; // Chỉ giữ lại class này
//         });
//     });
// });
//
//
// $(document).on('click', '.js_btn_subscribe', function () {
//     const $btn = $(this);
//     const originalText = $btn.text(); // lưu lại text gốc
//     const $form = $btn.closest('form');
//     const email = $form.find('input[name="email"]').val();
//     const url = $btn.data('url');
//     const $response = $form.find('.js_response');
//
//     $btn.prop('disabled', true).text('Submitting...');
//     // Reset thông báo
//     $response.removeClass('tw-text-red tw-text-white').text('');
//
//     if (!email) {
//         $response.addClass('tw-text-red').text('Please enter your email.');
//         $btn.prop('disabled', false).text(originalText);
//         return;
//     }
//
//     $.ajax({
//         method: 'POST',
//         url: url,
//         data: {
//             email: email
//         },
//         success: function (res) {
//             $response.addClass('tw-text-white').text(res.message || 'Thank you for subscribing!');
//             $form[0].reset();
//         },
//         error: function (xhr) {
//             let message = 'Something went wrong!';
//             if (xhr.responseJSON && xhr.responseJSON.message) {
//                 message = xhr.responseJSON.message;
//             }
//             $response.addClass('tw-text-red').text(message);
//         },
//         complete: function () {
//             // luôn luôn khôi phục lại nút
//             $btn.prop('disabled', false).text(originalText);
//         }
//     });
// });