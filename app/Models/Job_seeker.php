<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job_seeker extends Model
{
    use HasFactory;
    protected $fillable = ["username", "full_name", "birth_date", "password"];

    public function certificates()
    {
        return $this->hasMany(Certificate::class, "job_seeker_id");
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, "job_seekers_skills", "job_seeker_id", "skill_id");
    }

    public function contacts()
    {
        return $this->hasMany(Contact_information::class, "job_seeker_id");
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, "job_seekers_offers", "job_seeker_id", "offer_id");
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, "company_job_seeker", "job_seeker_id", "company_id");
    }

    public function posts()
    {
        return $this->hasMany(Post::class, "job_seeker_id");
    }

    public function comments()
    {
        return $this->hasMany(Comment::Class, "job_seeker_id");
    }



    public function postLikes(){
        return $this->hasMany(Post_like::class , "job_seeker_id");
    }
    public function commentLikes(){
        return $this->hasMany(Comment_like::class , "job_seeker_id");
    }

    public function portfolio(){
        return $this->hasMany(Portfolio::class,"job_seeker_id");
    }
}
