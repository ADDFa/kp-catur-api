<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\IncomingLetter;
use App\Models\Letter;
use App\Models\OutgoingLetter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    private function getRules(array $defaultRule): array
    {
        $rules = [
            "reference_number"  => "required|string",
            "date"              => "required|date_format:Y-m-d",
            "letter_type"       => "required|string",
            "category"          => [
                "required",
                Rule::in(["penting", "mendesak", "biasa"])
            ],
            "regarding"         => "required|string"
        ];

        $rules += $defaultRule;
        return $rules;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type)
    {
        if ($type !== "incoming" && $type !== "outgoing") {
            return response()->json(Api::fails('Pilih tipe "incoming" atau "outgoing"'), 400);
        }

        $result = $type === "incoming" ?
            IncomingLetter::with("letter") :
            OutgoingLetter::with("letter");

        if ($request->range) {
            $result = $result->whereHas("letter", function (Builder $query) use ($request) {
                $query->whereBetween("letters.date", explode("_", $request->range));
            });
        }
        if ($request->take) $result->take($request->take);

        return response()->json(Api::success($result->get()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $type)
    {
        if ($type !== "incoming" && $type !== "outgoing") {
            return response()->json(Api::fails('Pilih tipe "incoming" atau "outgoing"'), 400);
        }

        $defaultRule = ["destination" => "required|string"];
        if ($type === "incoming") $defaultRule = ["sender" => "required|string"];

        $validator = Validator::make($request->all(), $this->getRules($defaultRule));
        if ($validator->fails()) return response()->json(Api::errors($validator->errors()), 400);

        $result = DB::transaction(function () use ($request, $type) {
            // create letter
            $letter = new Letter;
            $letter->reference_number = $request->reference_number;
            $letter->date = $request->date;
            $letter->letter_type = $request->letter_type;
            $letter->category = $request->category;
            $letter->regarding = $request->regarding;
            $letter->save();

            // create incoming/outgoing letter
            if ($type === "incoming") {
                $incomingLetter = new IncomingLetter;
                $incomingLetter->letter_id = $letter->id;
                $incomingLetter->sender = $request->sender;
                $incomingLetter->save();
            }

            if ($type === "outgoing") {
                $outgoingLetter = new OutgoingLetter;
                $outgoingLetter->letter_id = $letter->id;
                $outgoingLetter->destination = $request->destination;
                $outgoingLetter->save();
            }

            return $letter->with($type)->find($letter->id);
        });

        return response()->json(Api::success($result));
    }

    /**
     * Display the specified resource.
     *
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function show($type, Letter $letter)
    {
        if ($type !== "incoming" && $type !== "outgoing") {
            return response()->json(Api::fails('Pilih tipe "incoming" atau "outgoing"'), 400);
        }

        $letter = $type === "incoming" ?
            IncomingLetter::with("letter")->find($letter->id) :
            OutgoingLetter::with("letter")->find($letter->id);

        return response()->json(Api::success($letter));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, Letter $letter)
    {
        if ($type !== "incoming" && $type !== "outgoing") {
            return response()->json(Api::fails('Pilih tipe "incoming" atau "outgoing"'), 400);
        }

        $incomingLetter = IncomingLetter::find($letter->id);
        $defaultRule = ["destination" => "required|string"];
        if ($incomingLetter) $defaultRule = ["sender" => "required|string"];

        $validator = Validator::make($request->all(), $this->getRules($defaultRule));
        if ($validator->fails()) return response()->json(Api::errors($validator->errors()), 400);

        // update letter
        $letter->reference_number = $request->reference_number;
        $letter->date = $request->date;
        $letter->letter_type = $request->letter_type;
        $letter->category = $request->category;
        $letter->regarding = $request->regarding;
        $letter->save();

        // update incoming/outgoing letter
        if ($incomingLetter) {
            $incomingLetter->sender = $request->sender;
            $incomingLetter->save();
        } else {
            $outgoingLetter = OutgoingLetter::find($letter->id);
            $outgoingLetter->destination = $request->destination;
            $outgoingLetter->save();
        }

        $type = $incomingLetter ? "incoming" : "outgoing";
        return $letter->with($type)->find($letter->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Letter  $letter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Letter $letter)
    {
        return response()->json(Api::success($letter->delete()));
    }
}
