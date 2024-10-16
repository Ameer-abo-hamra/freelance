<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Contact_information;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory, HasApiTokens;
    protected $fillable = ["username", "full_name", "email", "password", "wallet", "verificationCode", "isActive", "birth_date","type","profile_photo",'id'];
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

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, "likeable");
    }
    public function services()
    {
        return $this->hasMany(Service::class, "customer_id");
    }

    public function coantacts()
    {
        return $this->morphMany(Contact_information::class, "contactable");
    }

    public function sendReport()
    {
        return $this->morphMany(Report::class, "reporter");
    }

    public function receivedReport()
    {
        return $this->morphMany(Report::class, "reported");
    }

    public function posts()
    {
        return $this->morphMany(Post::class, "postable");
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, "commentable");
    }

    public function followMade() {
        return $this->morphMany(Follow::class , "followMaker");

    }

    public function followRecived() {
        return $this->morphMany(Follow::class , "followReciver");

    }
    public function sender()
    {
        return $this->morphMany(Message::class, "sender");
    }
    public function reciver()
    {
        return $this->morphMany(Message::class, "reciver");
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('username', 'like', '%' . $term . '%')
                    ->orWhere('full_name', 'like', '%' . $term . '%')
                    ->orWhere('email', 'like', '%' . $term . '%');
    }
    public function notificationSent() {

        return $this->morphMany(Notification::class ,"notfiSender" );
    }

    public function notificationReciver() {

        return $this->morphMany(Notification::class ,"notfiReciver" );
    }

}
