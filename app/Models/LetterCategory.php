<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterCategory extends Model
{
    use HasFactory;

    public function letter()
    {
        return $this->hasMany(Letter::class);
    }
}
