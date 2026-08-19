<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Gate;

class Member extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function children()
    {
        return $this->hasMany(Member::class, 'parent_id');
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('member/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_member_' . $action,
                ];
            }
        }

        return $options;
    }
}

