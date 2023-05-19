<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    use HasFactory;

    protected $primaryKey = "username";
    protected $hidden = ["password"];
    public $incrementing = false;

    public function user()
    {
        return $this->hasOne("users");
    }
}
