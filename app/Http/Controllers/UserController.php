<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\Credential;
use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "take"  => "integer",
            "name"  => "string"
        ]);

        if ($validator->fails()) return response()->json(Api::errors($validator->errors()));

        $result = User::whereHas("position", function (Builder $query) {
            $query->where("role", "!=", "operator");
        });

        if ($request->take) $result = $result->take($request->take);
        if ($request->name) $result = $result->where("name", "like", "%{$request->name}%");

        return response()->json(Api::success($result->get()));
    }

    public function show(User $user)
    {
        $result = $user->whereHas("position", function (Builder $query) {
            $query->where("role", "!=", "operator");
        })->find($user->id);

        return response()->json(Api::success($result));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"      => "required|string",
            "role"      => [
                "required",
                "string",
                Rule::in(["staff", "kepsek", "wakil_kepsek"])
            ],
            "username"  => "required|string|unique:credentials,username"
        ]);

        if ($validator->fails()) return response()->json(Api::errors($validator->errors()));

        $result = DB::transaction(function () use ($request) {
            // crate new user
            $user = new User;
            $user->name = $request->name;
            $user->save();

            // set user position
            $userPositon = new UserPosition;
            $userPositon->user_id = $user->id;
            $userPositon->role = $request->role;
            $userPositon->save();

            // crate account(credentials)
            $credential = new Credential;
            $credential->user_id = $user->id;
            $credential->username = $request->username;
            $credential->password = password_hash("password", PASSWORD_DEFAULT);
            $credential->save();

            return [
                "user"          => $user,
                "credential"    => [
                    "username"  => $request->username,
                    "password"  => "password"
                ]
            ];
        });

        return response()->json(Api::success($result));
    }

    public function destroy(User $user)
    {
        $user = User::with("position")->find($user->id);
        if ($user->position->role === "operator") return response()->json(Api::fails("User tidak ditemukan"), 404);
        return Api::success($user->delete());
    }
}
