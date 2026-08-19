<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $fillable = ['state'];

    const int TYPE_SUBSCRIBER = 0;
    const int TYPE_BOOKING = 1;
    const int TYPE_FEEDBACK = 2;

    public static function getFeedbackNotSeen()
    {
        static $cached = [];
        if (!empty($cached['feedback_not_seen'])) {
            return $cached['feedback_not_seen'];
        }
        $cached['feedback_not_seen'] = Feedback::where('state', 0)->limit(10)->get();;
        return $cached['feedback_not_seen'];
    }

    public function dealer()
    {
        return $this->belongsTo(Member::class, 'dealer_id', 'id');
    }

    public function getDealerNameAttribute(): string
    {
        if ($this->dealer) {
            return $this->dealer->user_name . ' - ' . $this->dealer->name;
        }
        return '';
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('feedback/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_feedback_' . $action,
                ];
            }
        }

        return $options;
    }
}
