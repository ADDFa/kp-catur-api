<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\NumberOfLetter;

class NumberOfLetterController extends Controller
{
    public function show()
    {
        return response()->json(Api::success(NumberOfLetter::first()));
    }
}
