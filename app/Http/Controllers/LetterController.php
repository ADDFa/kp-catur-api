<?php

namespace App\Http\Controllers;

use App\Http\Helper\Filters;
use App\Http\Helper\Response;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            "number"        => "required|string",
            "type"          => "required|string",
            "category"      => "required|exists:letter_categories,id",
            "regarding"     => "required|string",
            "as"            => [
                "required",
                Rule::in(["in", "out"])
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = Letter::orderBy("created_at", "desc");
        $result = new Filters((new Letter), $request);
        $result = $result->after()->before()->result()->get();
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);


        $letter = Letter::create([
            "number"                => $request->number,
            "type"                  => $request->type,
            "letter_category_id"    => $request->category,
            "regarding"             => $request->regarding
        ]);

        $controller = ($request->as === "in") ? "IncomingLetterController" : "OutgoingLetterController";
        return App::call("App\Http\Controllers\\{$controller}@store", ["letterId" => $letter->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function show(Letter $letter)
    {
        return Response::success($letter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Letter $letter)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $letter->number = $request->number;
        $letter->type = $request->type;
        $letter->letter_category_id = $request->category;
        $letter->regarding = $request->regarding;
        $letter->save();

        $controller = ($request->as === "in") ? "IncomingLetterController" : "OutgoingLetterController";

        return app()->call("\App\Http\Controllers\\{$controller}@update", ["letterId" => $letter->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Letter $letter)
    {
        if (!$request->as) return Response::fails("Pilih jenis surat masuk/keluar");
        if ($request->as === "in") {
            $incomingLetter = \App\Models\IncomingLetter::find($letter->id);
            Storage::delete($incomingLetter->letter_image);
        }

        return Response::success($letter->delete());
    }

    public function total()
    {
        $totalIn = app()->call("\App\Http\Controllers\IncomingLetterController@total");
        $totalOut = app()->call("\App\Http\Controllers\OutgoingLetterController@total");
        return Response::success([
            "incoming"  => $totalIn,
            "outgoing"  => $totalOut
        ]);
    }
}
