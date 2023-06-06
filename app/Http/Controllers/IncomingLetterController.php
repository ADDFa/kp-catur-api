<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\Disposition;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncomingLetterController extends Controller
{
    public function disposition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "incoming_letter_id"    => "required|exists:incoming_letters,letter_id",
            "to"                    => "required|exists:users_position,user_id",
            "message"               => "required|string"
        ]);

        if ($validator->fails()) return response()->json(Api::errors($validator->errors()), 400);

        $disposition = Disposition::where("incoming_letter_id", $request->incoming_letter_id)->where("to", $request->to)->first();
        if (!$disposition) {
            $disposition = new Disposition;
            $disposition->incoming_letter_id = $request->incoming_letter_id;
            $disposition->to = $request->to;
            $disposition->message = $request->message;
            $disposition->save();
        }

        $incomingLetter = IncomingLetter::find($request->incoming_letter_id);
        $incomingLetter->disposition_status = "process";
        $incomingLetter->save();

        return response()->json(Api::success($disposition));
    }
}
