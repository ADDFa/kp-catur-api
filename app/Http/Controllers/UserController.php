<?php

namespace App\Http\Controllers;

use App\Http\Helper\Filters;
use App\Http\Helper\Response;
use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            "name"          => "required|string",
            "role"          => "required|exists:roles,id",
            "username"      => "required|unique:credentials,username",
            "password"      => "required|min:8"
        ];
    }

    public function index(Request $request)
    {
        $result = User::with("role");
        $result = new Filters($result, $request);
        $result = $result->search("name")->before()->after()->result()->get();
        return Response::success($result);
    }

    public function show(User $user)
    {
        return Response::success($user::with("role")->find($user->id));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $checkKepsek = User::with("role")->where("role_id", $request->role)->first();
        if ($checkKepsek && ($checkKepsek->role->role === "Kepala Sekolah")) {
            return Response::fails("Kepala Sekolah Telah Terdaftar", 400);
        }

        $result = DB::transaction(function () use ($request) {
            // crate new user
            $user = new User;
            $user->name = $request->name;
            $user->role_id = $request->role;
            $user->save();

            // crate account(credentials)
            $credential = new Credential;
            $credential->user_id = $user->id;
            $credential->username = $request->username;
            $credential->password = password_hash($request->password, PASSWORD_DEFAULT);
            $credential->save();

            return $user;
        });

        return Response::success($result);
    }

    public function update(Request $request, User $user)
    {
        unset($this->rules["username"]);
        unset($this->rules["password"]);

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) return Response::errors($validator);

        $checkKepsek = User::with("role")->where("role_id", $request->role)->first();
        if ($checkKepsek && ($checkKepsek->role->role === "Kepala Sekolah")) {
            return Response::fails("Kepala Sekolah Telah Terdaftar", 400);
        }

        $user->name = $request->name;
        $user->role_id = $request->role;
        $user->save();

        return Response::success($user);
    }

    public function destroy(User $user)
    {
        return Response::success($user->delete());
    }

    public function total()
    {
        $total = User::all()->count();
        return Response::success($total);
    }
}
