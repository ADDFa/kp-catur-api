<?php

namespace App\Http\Res;

class Api
{
    public static function errors($errors)
    {
        return ["errors" => $errors];
    }

    public static function fails($message = "")
    {
        return ["message" => $message];
    }

    public static function success($data = null)
    {
        return ["data" => $data];
    }
}
