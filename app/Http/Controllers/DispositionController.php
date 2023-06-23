<?php

namespace App\Http\Controllers;

use App\Http\Helper\Response;
use App\Models\Disposition;
use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DispositionController extends Controller
{
    public function index(Request $request)
    {
        $result = Disposition::with(["incomingLetter", "user"]);
        $user = User::with("role")->find($request->user->id);
        if ($user->role->role === "Operator") return $result->get();

        return $result->where("user_id", $user->id)->get();
    }

    public function show(Disposition $disposition)
    {
        return $disposition->with(["incomingLetter", "user", "incomingLetter.letter"])->where("id", $disposition->id)->first();
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "incoming_letter_id"    => "required|exists:incoming_letters,letter_id",
            "user_id"               => "required|exists:users,id",
            "message"               => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $user = User::with("role")->find($request->user_id);
        if ($user->role_id === $request->user->role_id || $user->role->role === "Operator") {
            return Response::fails("Gagal mendisposisikan", 400);
        }

        $incomingLetter = IncomingLetter::find($request->incoming_letter_id);
        $incomingLetter->disposition_status = "process";
        $incomingLetter->save();

        $disposition = Disposition::updateOrCreate([
            "user_id"               => $request->user_id,
            "incoming_letter_id"    => $request->incoming_letter_id
        ], [
            "message"               => $request->message
        ]);

        return Response::success(["disposition" => $disposition, "incoming_letter" => $incomingLetter]);
    }

    public function dispositionFinish(Disposition $disposition)
    {
        $incomingLetter = IncomingLetter::find($disposition->incoming_letter_id);
        $incomingLetter->disposition_status = "finish";
        $incomingLetter->save();
        return Response::success($incomingLetter);
    }

    public function keepOnDisposition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id"           => "required|exists:users,id",
            "disposition_id"    => "required|exists:dispositions,id"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $disposition = Disposition::find($request->disposition_id);
        $disposition->user_id = $request->user_id;
        $disposition->save();

        return Response::fails("Berhasil meneruskan disposisi", 200);
    }
}
