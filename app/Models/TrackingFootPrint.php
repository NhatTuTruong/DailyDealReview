<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingFootPrint extends Model
{
    protected $table = 'tracking_footprint';
    protected $fillable = ['ukey', 'request', 'referer', 'ip', 'user_agent', 'country', 'country_code'];
}
