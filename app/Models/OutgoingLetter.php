<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $primaryKey = "letter_id";
    protected $guarded = [];
    public $incrementing = false;

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
