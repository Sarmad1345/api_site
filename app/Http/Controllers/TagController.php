<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    function getTags()
    {
        return Tag::orderBy('name')->get();
    }
}
