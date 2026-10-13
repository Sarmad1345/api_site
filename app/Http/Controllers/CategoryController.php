<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    function getCategories()
    {
        return Category::orderBy('name')->get();
    }
}
