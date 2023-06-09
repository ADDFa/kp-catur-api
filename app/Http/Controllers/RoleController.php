<?php

namespace App\Http\Controllers;

use App\Http\Helper\Response;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Response::success(Role::all());
    }
}
