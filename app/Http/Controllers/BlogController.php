<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\Image;

class BlogController extends Controller
{
    function getBlogs()
    {
        return Blog::with(['category', 'tags', 'image'])->latest('updated_at')->paginate(10);
    }

    function getBlog(int $id)
    {
        $blog = Blog::with(['category', 'tags', 'image'])->find($id);
        if (!$blog) {
            return response()->json(["result" => "Blog not found"], 404);
        }
        return $blog;
    }

    function addBlogs(BlogRequest $request)
    {
        $path = $request->file('file')->store('images', 'public');
        $image = Image::create([
            "path" => $path,
        ]);

        $blog = Blog::create([
            "email" => $request->email,
            "title" => $request->title,
            "description" => $request->description,
            "image_id" => $image->id,
            "category_id" => $request->category_id,
        ]);
        $blog->tags()->attach($request->tags ?? []);

        return ["result" => "Data has been saved", "blog" => $blog->load(['category', 'tags', 'image'])];
    }

    function updateBlogs(BlogRequest $request)
    {
        $blog = Blog::find($request->id);
        if (!$blog) {
            return response()->json(["result" => "Blog not found"], 404);
        }

        $data = [
            "email" => $request->email,
            "title" => $request->title,
            "description" => $request->description,
            "category_id" => $request->category_id,
        ];
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('images', 'public');
            $data["image_id"] = Image::create(["path" => $path])->id;
        }

        $blog->update($data);
        if ($request->has('tags')) {
            $blog->tags()->sync($request->tags ?? []);
        }

        return ["result" => "Data has been updated", "blog" => $blog->load(['category', 'tags', 'image'])];
    }

    function deleteBlog(int $id)
    {
        if (!Blog::find($id)) {
            return response()->json(["result" => "Blog not found"], 404);
        }
        $data = Blog::destroy($id);
        if ($data) {
            return ["result" => "data has been deleted"];
        } else {
            return ["result" => "Data not Deleted"];
        }
    }
    function searchBlogs()
    {
        $searchTerm = request('search');
        return Blog::with(['category', 'tags', 'image'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->paginate(10);
    }

    function trashedBlogs()
    {
        return Blog::onlyTrashed()->latest('deleted_at')->get();
    }

    function restoreBlog(int $id)
    {
        $blog = Blog::onlyTrashed()->find($id);
        if (!$blog) {
            return response()->json(["result" => "Blog not found in trash"], 404);
        }
        $blog->restore();
        return ["result" => "Blog restored"];
    }

    function forceDeleteBlog(int $id)
    {
        $blog = Blog::withTrashed()->find($id);
        if (!$blog) {
            return response()->json(["result" => "Blog not found"], 404);
        }
        $blog->forceDelete();
        return ["result" => "Blog permanently deleted"];
    }
}
