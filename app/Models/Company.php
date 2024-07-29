<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ["id", "name", "establishment_date", "employee_number", "password", "verificationCode", "isActive", "email","type","profile_photo"];
    protected $hidden = ["created_at", "updated_at"];
    public function offers()
    {
        return $this->hasMany(Offer::class, "company_id");
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class,"company_id");
    }

    // public function services()
    // {
    //     return $this->morphMany(Service::class, "serviceable");
    // }
    public function makeApply()
    {
        return $this->morphMany(ServiceApply::class, "applyable");
    }


    public function comment()
    {
        return $this->morphMany(Comment::class, "commentable");
    }


    public function coantacts()
    {
        return $this->morphMany(Contact_information::class, "contactable");
    }

    public function jobSeekers()
    {
        return $this->belongsToMany(Job_seeker::class, "company_job_seeker", "company_id", "job_seeker_id");
    }

    public function posts()
    {
        return $this->morphMany(Post::class, "postable");
    }


    public function portfolio()
    {
        return $this->morphMany(Portfolio::class, "portfolioable");
    }

    public function sendReport()
    {
        return $this->morphMany(Report::class, "reporter");
    }

    public function receivedReport()
    {
        return $this->morphMany(Report::class, "reported");
    }

    public function likes()
    {
        return $this->morphMany(Like::class, "likeable");
    }


    public function reportsMade()
    {
        return $this->morphMany(Report::class, "reporter");
    }
    public function reportsReceived()
    {
        return $this->morphMany(Report::class, "reported");
    }

    public function followMade() {
        return $this->morphMany(Follow::class , "followMaker");

    }

    public function followRecived() {
        return $this->morphMany(Follow::class , "followReciver");

    }

    public function notificationSent() {

        return $this->morphMany(Notification::class ,"notfiSender" );
    }

    public function notificationReciver() {

        return $this->morphMany(Notification::class ,"notfiReciver" );
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

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('email', 'like', '%' . $term . '%');
    }

}
