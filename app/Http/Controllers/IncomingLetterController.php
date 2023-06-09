<?php

namespace App\Http\Controllers;

use App\Http\Helper\Filters;
use App\Http\Helper\Response;
use App\Models\Disposition;
use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncomingLetterController extends Controller
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            "sender"        => "required|string",
            "letter_image"  => [
                "required",
                Rule::file()->types(["pdf"])->max(2048)
            ]
        ];
    }

    public function index(Request $request)
    {
        $result = IncomingLetter::with("letter")->orderBy("created_at", "desc");
        $result = new Filters($result, $request);
        $result = $result->before()->after()->result()->get();
        return Response::success($result);
    }

    public function show(IncomingLetter $incomingLetter)
    {
        return Response::success($incomingLetter->with("letter")->find($incomingLetter->letter_id));
    }

    public function store(Request $request, $letterId)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $letter_image = $request->file("letter_image")->store("letters");
        $result = IncomingLetter::create([
            "letter_id"     => $letterId,
            "sender"        => $request->sender,
            "letter_image"  => $letter_image
        ]);

        return Response::success($result);
    }

    public function update(Request $request, $letterId)
    {
        $rules = $this->rules;
        if (!$request->file("letter_image")) unset($rules["letter_image"]);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return Response::errors($validator);

        $letter = IncomingLetter::find($letterId);
        if ($request->file("letter_image")) {
            $letter_image = $request->file("letter_image")->store("letters");
            Storage::delete($letter->letter_image);
            $letter->letter_image = $letter_image;
        }

        $letter->sender = $request->sender;
        $letter->save();

        return Response::success($letter);
    }

    public function disposition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "incoming_letter_id"    => "required|exists:incoming_letters,letter_id",
            "user_id"               => "required|exists:user,id",
            "message"               => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $user = User::with("role")->find($request->user_id);
        if ($user->role_id === $request->user->role_id || $user->role->role === "Operator") {
            return Response::fails("Gagal mendisposisikan", 400);
        }

        $incomingLetter = IncomingLetter::find($request->incoming_letter_id);
        $incomingLetter->total_disposition += 1;
        $incomingLetter->save();

        $disposition = new Disposition;
        $disposition->incoming_letter_id = $request->incoming_letter_id;
        $disposition->user_id = $request->user_id;
        $disposition->message = $request->message;
        $disposition->save();

        return Response::success($disposition);
    }

    public function dispositionFinis(Disposition $disposition)
    {
        $disposition->disposition_status = "finish";
        return Response::success($disposition);
    }

    public function total()
    {
        return IncomingLetter::all()->count();
    }
}
