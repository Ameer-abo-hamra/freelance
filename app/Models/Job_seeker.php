<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Job_seeker extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable = ["username", "full_name", "birth_date", "password", "email", "verificationCode", "isActive"];
    protected $hidden = ["created_at", "updated_at"];
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

    // public function services()
    // {
    //     return $this->morphMany(Service::class, "serviceable");
    // }
    public function makeApply(){
        return $this->morphMany(ServiceApply::class,"applyable");
    }
    public function certificates()
    {
        return $this->hasMany(Certificate::class, "job_seeker_id");
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, "job_seekers_skills", "job_seeker_id", "skill_id");
    }

    public function coantacts()
    {
        return $this->morphMany(Contact_information::class, "contactable");
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
        return $this->morphMany(Post::class, "postable");
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, "commentable");
    }


    public function likes()
    {
        return $this->morphMany(Like::class, "likeable");
    }

    public function portfolio()
    {
        return $this->morphTo(Portfolio::class, "portfolioable");
    }

    public function sendReport()
    {
        return $this->morphMany(Report::class, "reporter");
    }

    public function receivedReport()
    {
        return $this->morphMany(Report::class, "reported");
    }
}
