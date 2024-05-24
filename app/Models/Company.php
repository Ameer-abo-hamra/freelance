<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ["id","name", "establishment_date", "employee_number", "password", "verificationCode", "isActive","email"];
    protected $hidden = ["created_at", "updated_at"];
    public function offers()
    {
        return $this->hasMany(Offer::Class,"job_seeker_id");
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

    public function portfolio()
    {
        return $this->hasMany(Portfolio::class, "company_id");
    }

    public function sendReport(){
        return $this->morphMany(Report::class,"reporter");
    }

    public function receivedReport(){
        return $this->morphMany(Report::class,"reported");
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
