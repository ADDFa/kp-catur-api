<?php

namespace App\Http\Controllers;

use App\Http\Helper\Filters;
use App\Http\Helper\Response;
use App\Models\IncomingLetter;
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

    public function report(Request $request)
    {
        $result = IncomingLetter::with(["letter", "letter.category"])->orderBy("created_at", "desc");
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

    public function total()
    {
        return IncomingLetter::all()->count();
    }

    public function showLetterFile(Request $request)
    {
        if (!$request->file_name) {
            return Response::fails("Nama File Tidak Ada!", 400);
        }

        $exists = Storage::exists("{$request->file_name}", "local");
        if (!$exists) return Response::fails("File tidak ada!");

        $file = Storage::get($request->file_name);
        return $file;
    }
}
