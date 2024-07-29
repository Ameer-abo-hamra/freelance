<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ["title", "body", "job_seeker_id", "company_id", "postable_id", "postable_type"];

    public function comments()
    {
        return $this->hasMany(Comment::class, "post_id");
    }
    public function likes()
    {
        return $this->morphMany(Like::class, "likeable");
    }

    public function postable()
    {
        return $this->morphTo();
    }

    public function reportRecived()
    {
        return $this->morphMany(Report::class, "reported");
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', '%' . $term . '%')
                    ->orWhere('body', 'like', '%' . $term . '%');
    }

}
