<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ["title", "body"];
    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function post()
    {
        return $this->belongsTo(Post::class, "post_id");
    }

    public function likes()
    {
        return $this->hasMany(Comment_like::class, "comment_id");
    }
}
