<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    const int ROOT_ADMIN_ID = 1;
    const array PATTERN_PASSWORD = [
        'PATTERN' => '/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/',
        'MESSAGE' => 'Mật khẩu phải lớn hơn 8 ký tự và chứa ít nhất 1 chữ viết hoa, 1 ký tự đặc biệt'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }


    public function getGroupNameAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }

        return data_get($this, 'group.name', 'Guest');
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1 || $this->is_super_admin;
    }

    public function checkPermission($perm_key = false): bool
    {
        if (!$perm_key) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $permission_data = data_get($this, 'group.permission_data', []);
        $check_flag = false;

        if (preg_match('/[|&]/', $perm_key)) {
            $perm_key = preg_replace('/([^a-zA-Z0-9\/_.\-:()|&])/', '', $perm_key);
            $perm_key = preg_replace('/(([^|&])([|&])([|&]+))/', '\\2\\3', $perm_key);
            $perm_key = preg_replace_callback('/([a-zA-Z0-9\/_.\-:]+)/', function ($matches) use ($permission_data) {
                return in_array($matches[1], $permission_data) ? '+' : '*';
            }, $perm_key);
            $perm_key = str_replace(['+', '*', '|', '&'], [' true ', ' false ', ' || ', ' && '], $perm_key);

            eval('$check_flag = ' . $perm_key . ';');
        } else {
            $check_flag = in_array($perm_key, $permission_data);
        }

        return $check_flag;
    }

    public static function makeListUser($selected_id = '', $include_default = false)
    {
        $users = User::orderBy('name')->where('id', '<>', self::ROOT_ADMIN_ID)->get(['id', 'name']);
        $html = '';

        if ($include_default) {
            $html .= "<option value='0'>__NONE__</option>";
        }

        foreach ($users as $user) {
            $selected = ($user->id == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$user->id\" $selected>" . $user->name . "</option>";
        }
        return $html;

    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('user/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_user_' . $action,
                ];
            }
        }

        return $options;
    }
}
