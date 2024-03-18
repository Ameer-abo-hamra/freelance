<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ["name", "establishment_date", "employee_number"];

    public function offers()
    {
        return $this->hasMany(Offer::Class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::Class, "company_id");
    }
    public function commentLikes()
    {
        return $this->hasMany(Comment_like::class, "company_id");
    }
    public function contactInformations()
    {
        return $this->hasMany(Contact_information::class, "company_id");
    }

    public function jobSeekers()
    {
        return $this->belongsToMany(Job_seeker::class, "company_job_seeker", "company_id", "job_seeker_id");
    }
    public function posts()
    {
        return $this->hasMany(Post::class, "company_id");
    }

    public function postLikes()
    {
        return $this->hasMany(Post_like::Class, "company_id");
    }

    public function portfolio(){
        return $this->hasMany(Portfolio::class,"company_id");
    }

}
