<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public static function makeListProvince($selected_code_name = '', $order_by = 'code', $value_by_name = false, $get_full_name = false): string
    {
        $provinces = Province::orderBy($order_by)->get();
        $html = '';

        foreach ($provinces as $province) {
            $value = $value_by_name ? $province->name : $province->code_name;
            $option_name = $get_full_name ? $province->full_name : $province->name;
            $selected = ($province->code_name == $selected_code_name) ? 'selected' : '';
            $html .= "<option value=\"$value\" $selected>" . $option_name . "</option>";
        }
        return $html;
    }

    public static function getAllProvinceHasDealer()
    {
        $list_province_has_dealer = Member::select('province_code')->distinct()->pluck('province_code')->toArray();
        return Province::whereIn('code_name', $list_province_has_dealer)->orderBy('name')->get();
    }
}
