<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CredentialController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "old_password"  => "required|string",
            "username"      => "required|string",
            "password"      => "required|string|min:8"
        ]);

        if ($validator->fails()) return response()->json(Api::errors($validator->errors()));

        $credential = Credential::where("user_id", $request->user->id)->first();

        // cek old password
        if (!password_verify($request->old_password, $credential->password)) {
            return response()->json(Api::fails("Password salah!"));
        }

        $credential->username = $request->username;
        $credential->password = password_hash($request->password, PASSWORD_DEFAULT);
        $credential->save();

        return $this->tokens(User::with("position")->find($credential->user_id));
    }
}
