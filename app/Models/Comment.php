<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ["title", "body", "commentable_id", "commentable_type", "user_id", "user_type","post_id"];

    public function commentable()
    {
        return $this->morphTo();
    }
    public function post()
    {
        return $this->belongsTo(Post::class, "post_id");
    }
    public function likes()
    {
        return $this->morphMany(Like::class, "likeable");
    }
}
