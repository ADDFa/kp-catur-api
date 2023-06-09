<?php

namespace App\Http\Controllers;

use App\Models\LetterCategory;

class LetterCategoryController extends Controller
{
    public function index()
    {
        return LetterCategory::all();
    }
}
