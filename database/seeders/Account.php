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
            [
                "name"      => "Admin"
            ],
            [
                "name"      => "Kepala Sekolah"
            ],
            [
                "name"      => "Wakil Kepala Sekolah"
            ],
            [
                "name"      => "Staff"
            ]
        ]);

        DB::table("credentials")->insert([
            [
                "user_id"   => 1,
                "username"  => "Admin",
                "password"  => password_hash("password", PASSWORD_DEFAULT)
            ],
            [
                "user_id"   => 2,
                "username"  => "kepsek",
                "password"  => password_hash("password", PASSWORD_DEFAULT)
            ],
            [
                "user_id"   => 3,
                "username"  => "wakepsek",
                "password"  => password_hash("password", PASSWORD_DEFAULT)
            ],
            [
                "user_id"   => 4,
                "username"  => "staff",
                "password"  => password_hash("password", PASSWORD_DEFAULT)
            ]
        ]);

        DB::table("users_position")->insert([
            [
                "user_id"   => 1,
                "role"      => "operator"
            ],
            [
                "user_id"   => 2,
                "role"      => "kepsek"
            ],
            [
                "user_id"   => 3,
                "role"      => "wakil_kepsek"
            ],
            [
                "user_id"   => 4,
                "role"      => "staff"
            ]
        ]);
    }
}
