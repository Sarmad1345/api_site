<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }
}
