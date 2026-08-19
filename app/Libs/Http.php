<?php

namespace App\Libs;


class Http
{

    public static function responseMessage($message = '')
    {
        return response()->json([
            'message' => $message,
        ], 200, [], JSON_PRETTY_PRINT);
    }


    public static function responseError($message = '', $headerCode = 404)
    {
        return response()->json([
            'error' => ['code' => $headerCode, 'message' => $message],
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public static function responseData($data = [])
    {
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
}
