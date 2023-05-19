<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    public function incoming()
    {
        return $this->hasOne(IncomingLetter::class);
    }

    public function outgoing()
    {
        return $this->hasOne(OutgoingLetter::class);
    }
}
