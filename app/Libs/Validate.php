<?php

namespace App\Libs;

class Validate
{
    public static function validatePhoneNumber($phone = ''): false|int
    {
        return preg_match('/^(0[35789])+([0-9]{8})\b$/', $phone);
    }

    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function makeTime($date, $format = 'mdy'): false|int
    {
        $date = explode('-', $date);
        return mktime(0, 0, 0, $date[0], $date[1], $date[2]);
    }

    public static function isValidDate($date)
    {

    }
}
