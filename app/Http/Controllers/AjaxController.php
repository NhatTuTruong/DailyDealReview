<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getDistrict(Request $request)
    {
        $province_id = $request->get('province_id', 0);
        $province = Province::find($province_id);
        $data['districts'] = District::makeListDistrict($province_id, '');
        $data['price_ship'] = data_get($province, 'price_ship', 30000);
        return $this->responseJsonOk($data);
    }
}
