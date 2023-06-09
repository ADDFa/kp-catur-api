<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function incoming()
    {
        return $this->hasOne(IncomingLetter::class);
    }

    public function outgoing()
    {
        return $this->hasOne(OutgoingLetter::class);
    }

    public function category()
    {
        return $this->hasOne(LetterCategory::class);
    }
}
