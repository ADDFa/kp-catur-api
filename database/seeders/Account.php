<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Account extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "name"      => "Admin"
        ]);

        DB::table("credentials")->insert([
            "user_id"   => 1,
            "username"  => "Admin",
            "password"  => password_hash("password", PASSWORD_DEFAULT)
        ]);

        DB::table("users_position")->insert([
            "user_id"   => 1,
            "role"      => "operator"
        ]);
    }
}
