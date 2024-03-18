<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ["title", "body"];

    public function jobSeeker()
    {
        return $this->belongsTo(Job_seeker::class, "job_seeker_id");
    }
    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }
    public function comments()
    {
        return $this->hasMany(Comment::Class, "post_id");
    }
    public function likes(){
        return $this->hasMany(Post_like::class , "post_id");
    }
}
