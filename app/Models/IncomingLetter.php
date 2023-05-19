<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $primaryKey = "letter_id";

    public function letter()
    {
        return $this->hasOne(Letter::class, "id", "letter_id");
    }
}
