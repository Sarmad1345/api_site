<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    function getBlogs()
    {
        return Blog::all();
    }

    function addBlogs(Request $request)
    {
        $blogs = new Blog();
        $blogs->title = $request->title;
        $blogs->content = $request->content;
        if ($blogs->save()) {
            return ["result" => "Data has been saved"];
        } else {
            return ["result" => "Data not saved"];
        }
    }
}
