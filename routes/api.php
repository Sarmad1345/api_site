<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

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
Route::post("addBlog", [BlogController::class, "addBlogs"]);
