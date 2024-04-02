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
    protected $fillable = ["username", "full_name", "email", "password", "wallet", "verificationCode" ,"isActive","birth_date"];
    protected $hidden = ["created_at","updated_at"];

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
    public function services()
    {
        return $this->hasMany(Service::class, "customer_id");
    }

    public function coantactInformations()
    {
        return $this->hasMany(Contact_information::class, "customer_id");
    }
}
