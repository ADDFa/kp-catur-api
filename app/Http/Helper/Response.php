<?php

namespace App\Http\Helper;

class Response
{
    public static function errors(\Illuminate\Validation\Validator $validator, $status = 400)
    {
        return response()->json(["errors" => $validator->errors()], $status);
    }

    public static function fails(string $message, $status = 500)
    {
        return response()->json(["message" => $message], $status);
    }

    public static function success($data, $status = 200)
    {
        return response()->json(["data" => $data], $status);
    }
}
