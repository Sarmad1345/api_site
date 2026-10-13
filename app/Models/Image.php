<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = [
        'path',
    ];

    protected $appends = [
        'url',
    ];

    public function blog()
    {
        return $this->hasOne(Blog::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }
}
