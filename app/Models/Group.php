<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Group extends Model
{
    protected $casts = [
        'permission_data' => 'array',
    ];

    public static function makeListGroup($selected_id = ''): string
    {
        $groups = Group::orderBy('name')->get(['id', 'name']);
        $html = '';

        foreach ($groups as $group) {
            $selected = ($group->id == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$group->id\" $selected>" . $group->name . "</option>";
        }
        return $html;

    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('group/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_group_' . $action,
                ];
            }
        }

        return $options;
    }
}
