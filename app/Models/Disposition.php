<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function incomingLetter()
    {
        return $this->belongsTo(IncomingLetter::class, "incoming_letter_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
