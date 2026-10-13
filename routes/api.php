<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/test", function () {
    return [
        "name" => "Sarmad",
        "channel" => "coding with sarmad"
    ];
});

Route::get("blogs", [BlogController::class, "getBlogs"]);
Route::get("blog/{id}", [BlogController::class, "getBlog"]);
Route::post("addBlog", [BlogController::class, "addBlogs"]);
Route::put("updateBlog", [BlogController::class, "updateBlogs"]);
Route::delete("deleteBlog/{id}", [BlogController::class, "deleteBlog"]);

Route::get("categories", [CategoryController::class, "getCategories"]);
Route::get("tags", [TagController::class, "getTags"]);
