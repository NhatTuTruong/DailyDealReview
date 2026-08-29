<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Adult products',
            'Art',
            'Automobiles & Motorcycles',
            'Babies and kids',
            'Beauty & Health',
            'Bedding',
            'Books',
            'Clothing accessories',
            'Computers and accessories',
            'Decorations',
            'Drinks',
            'Education and Training',
            'Electronics and Technology',
            'Entertainment and media',
            'Equipment furniture',
            'Fashion jewelry',
            'Financial services and products',
            'Food',
            'For businesses',
            'Gaming and esports',
            'Hairdressing accessories',
            'Health',
            'Home Garden',
            'Houseware',
            'Pets',
            'Phone accessories',
            'Retail',
            'Shoes and sandals',
            'Software and services',
            'Sportswear',
            'Toys & Hobbies',
            'Travel',
            'Underwear',
            'Vehicle service',
        ];

        Category::query()
            ->where('type', Category::CATEGORY_TYPE_STORE)
            ->whereNotIn('name', $names)
            ->delete();

        foreach (config('app.available_locales', ['en']) as $language) {
            foreach ($names as $index => $name) {
                Category::query()->updateOrCreate(
                    [
                        'name' => $name,
                        'language' => $language,
                        'type' => Category::CATEGORY_TYPE_STORE,
                    ],
                    [
                        'slug' => Str::slug($name . '-' . $language . '-' . ($index + 1)),
                        'parent_id' => 0,
                        'priority' => count($names) - $index,
                        'status' => 1,
                    ]
                );

                Category::query()->updateOrCreate(
                    [
                        'name' => $name,
                        'language' => $language,
                        'type' => Category::CATEGORY_TYPE_POST,
                    ],
                    [
                        'slug' => Str::slug($name . '-post-' . $language . '-' . ($index + 1)),
                        'parent_id' => 0,
                        'priority' => count($names) - $index,
                        'status' => 1,
                        'at_home' => $index < 13 ? 1 : 0,
                    ]
                );
            }
        }
    }
}
