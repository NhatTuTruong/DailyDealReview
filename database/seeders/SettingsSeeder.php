<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('app.available_locales', ['en']) as $language) {
            foreach (Setting::defaultSettings() as $skey => $svalue) {
                Setting::query()->updateOrCreate(
                    ['language' => $language, 'skey' => $skey],
                    ['svalue' => (string) $svalue]
                );
            }
        }
    }
}
