$(document).on('click', '.js-remove_hour_item', function () {
    $(this).parent().parent().remove();
});

$(document).on('click', '.js-remove_flash_sale_item', function () {
    $(this).closest('.js-wrap_flash_sale').parent().remove();
});

$(document).on('click', '.js-add_hour_item', function () {
    let random_key = makeRandomStr(5);
    let key_name = $(this).closest('.js-flash_sale_item').find('input').data('key_name');
    let key_hour_start = "flash_sale_configs[" + key_name + "][hours][" + random_key + "][hour_start]";
    let key_hour_end = "flash_sale_configs[" + key_name + "][hours][" + random_key + "][hour_end]";

    let hour_item = $('<div />').addClass('choose-hour__item d-flex mt-3 justify-content-between js-hour_item');
    let hour_item_start = $('<div />').addClass('choose-hour__item__start').appendTo(hour_item);
    $('<label />').text('Giờ bắt đầu').appendTo(hour_item_start);
    let select_hour_start = $('<select />').attr('name', key_hour_start).appendTo(hour_item);

    list_hour_json.map(function (value, index) {
        $('<option />').attr('value', index).text(value).appendTo(select_hour_start);
    });

    let hour_item_end = $('<div />').addClass('choose-hour__item__end').appendTo(hour_item);
    $('<label />').text('Giờ kết thúc').appendTo(hour_item_end);
    let select_hour_end = $('<select />').attr('name', key_hour_end).appendTo(hour_item);

    list_hour_json.map(function (value, index) {
        $('<option />').attr('value', index).text(value).appendTo(select_hour_end);
    });

    let hour_item_remove = $('<div />').addClass('choose-hour__item--remove').appendTo(hour_item);
    $('<button />').addClass('button btn btn-sm btn-danger js-remove_hour_item').attr('type', 'button').text('Xóa').appendTo(hour_item_remove);

    hour_item.insertBefore($(this));
});

$('.js-add_flash_sale_item').on('click', function () {
    let wrap = $('<div />').addClass('col-lg-4');
    let wrap_fl_sale = $('<div />').addClass('wrap-flash-sale js-wrap_flash_sale').appendTo(wrap);
    let wrap_fl_sale_item = $('<div />').addClass('wrap-flash-sale__item mb-3 js-flash_sale_item').appendTo(wrap_fl_sale);
    let choose_date = $('<div />').addClass('choose-date').appendTo(wrap_fl_sale_item);
    $('<label />').text('Chọn ngày').appendTo(choose_date);

    let random_key = makeRandomStr(5);
    let input_name = "flash_sale_configs[" + random_key + "][date]";
    $('<input />').attr({
        'type': 'text',
        'required': 'required',
        // 'readonly': 'readonly',
        'name': input_name,
        'data-key_name': random_key
    }).addClass('form-control choose_date').appendTo(choose_date);
    let choose_hour = $('<div />').addClass('choose-hour').appendTo(wrap_fl_sale_item);
    $('<button />').addClass('btn btn-primary btn-sm w-100 mt-3 js-add_hour_item').text('Thêm giờ').attr('type', 'button').appendTo(choose_hour);
    $('<button />').addClass('btn btn-danger btn-sm w-100 mt-3 js-remove_flash_sale_item').text('Xóa').attr('type', 'button').appendTo(choose_hour);
    wrap.insertBefore($(this).parent());
});

