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
                "name"      => "Admin",
                "role_id"   => 4
            ],
            [
                "name"      => "Kepala Sekolah",
                "role_id"   => 2
            ],
            [
                "name"      => "Wakil Kepala Sekolah",
                "role_id"   => 3
            ],
            [
                "name"      => "Staff",
                "role_id"   => 1
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
    }
}
