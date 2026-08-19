<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    public static function getAll()
    {
        return Country::where('status', 1)->get();
    }
}
