<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function (User $user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        $permissions_configs = config('permission');

        foreach ($permissions_configs as $permission_key => $configs) {
            Gate::define($permission_key, function (User $user) use ($permission_key) {
                return $user->checkPermission($permission_key);
            });

            if (!empty($configs['items'])) {
                foreach ($configs['items'] as $config_key => $config) {
                    $level1_permission_key = $permission_key . '/' . $config_key;
                    Gate::define($level1_permission_key, function (User $user) use ($level1_permission_key) {
                        return $user->checkPermission($level1_permission_key);
                    });
                }
            }
        }
    }
}
