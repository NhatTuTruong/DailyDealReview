<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = 'wards';
    public $timestamps = false;

    public static function getWardName($district_id)
    {
        $district = self::where('id', $district_id)->select('name')->first();
        return data_get($district, 'name', '');
    }


    public static function makeListWard($district_code, $ward_code = '')
    {
        $wards = Ward::where('district_code', $district_code)
            ->orderBy('priority')->orderBy('name')
            ->get(['id', 'name', 'code', 'district_code']);

        if ($wards->count() < 1) {
            return '';
        }

        $html = '';
        foreach ($wards as $ward) {
            $selected = ($ward->code === $ward_code) ? 'selected' : '';
            $value = $ward->code;
            $option = $ward->name;
            $html .= "<option value=\"$value\" $selected>" . $option . "</option>";
        }
        return $html;

    }
}
