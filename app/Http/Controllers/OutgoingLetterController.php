<?php

namespace App\Http\Controllers;

use App\Http\Helper\Filters;
use App\Http\Helper\Response;
use App\Models\OutgoingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OutgoingLetterController extends Controller
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            "destination"   => "required|string"
        ];
    }

    public function index(Request $request)
    {
        $result = OutgoingLetter::with("letter")->orderBy("created_at", "desc");
        $result = new Filters($result, $request);
        $result = $result->before()->after()->result()->get();
        return Response::success($result);
    }

    public function show(OutgoingLetter $outgoingLetter)
    {
        return Response::success($outgoingLetter->with("letter")->find($outgoingLetter->letter_id));
    }

    public function store(Request $request, $letterId)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $result = OutgoingLetter::create([
            "letter_id"     => $letterId,
            "destination"   => $request->destination
        ]);

        return Response::success($result);
    }

    public function update(Request $request, OutgoingLetter $outgoingLetter)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $outgoingLetter->destination = $request->destination;
        $outgoingLetter->save();

        return Response::success($outgoingLetter);
    }

    public function total()
    {
        return OutgoingLetter::all()->count();
    }
}
