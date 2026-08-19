<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Slug extends Model
{
    const array MODULE = [
        'CATEGORY' => 'category',
        'POST' => 'post',
        'PAGE' => 'page'
    ];

    /**
     * @throws \Exception
     */
    public static function insertOrUpdateSlug($slug, $module, $module_id): void
    {
        if (!in_array($module, array_values(self::MODULE))) {
            throw new \Exception('Module invalid');
        }

        try {
            $existing_slug = Slug::where('module', $module)
                ->where('module_id', $module_id)->first();

            if ($existing_slug) {
                $existing_slug->slug = $slug;
                $existing_slug->module = $module;
                $existing_slug->module_id = $module_id;
                $existing_slug->save();
            } else {
                $new_slug = new Slug();
                $new_slug->slug = $slug;
                $new_slug->module = $module;
                $new_slug->module_id = $module_id;
                $new_slug->save();
            }
        } catch (\Exception $ex) {
            if (strpos($ex->getMessage(), '1062 Duplicate entry')) {
                throw new Exception('Slug đã tồn tại, vui lòng tạo slug khác');
            } else {
                throw new \Exception($ex->getMessage());
            }
        }
    }

    public static function deleteSlug($module, $module_id): void
    {
        try {
            Slug::where('module', $module)
                ->where('module_id', $module_id)->delete();
        } catch (Exception $ex) {
        }
    }
}
