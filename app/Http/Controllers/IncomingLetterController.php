<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncomingLetterController extends Controller
{
    public function disposition(Request $request, IncomingLetter $incomingLetter)
    {
        $validator = Validator::make($request->all(), [
            "disposition"   => "required|exists:users_position,user_id"
        ]);

        if ($validator->fails()) return response()->json(Api::errors($validator->errors()), 400);

        $incomingLetter->disposition = $request->disposition;
        $incomingLetter->save();
        return response()->json(Api::success($incomingLetter));
    }
}
