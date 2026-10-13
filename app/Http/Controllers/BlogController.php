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

    function getBlog(int $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(["result" => "Blog not found"], 404);
        }
        return $blog;
    }

    function addBlogs(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $blogs = new Blog();
        $blogs->title = $request->title;
        $blogs->content = $request->content;
        if ($blogs->save()) {
            return ["result" => "Data has been saved"];
        } else {
            return ["result" => "Data not saved"];
        }
    }

    function updateBlogs(Request $request)
    {
        $blogs = Blog::find($request->id);
        if (!$blogs) {
            return response()->json(["result" => "Blog not found"], 404);
        }
        $blogs->title = $request->title;
        $blogs->content = $request->content;
        if ($blogs->save()) {
            return ["result" => "Data has been updated"];
        } else {
            return ["result" => "Data not updated"];
        }
    }

    function deleteBlog(int $id)
    {
        $data = Blog::destroy($id);
        if ($data) {
            return ["result" => "data has been deleted"];
        } else {
            return ["result" => "Data not Deleted"];
        }
    }
}
