<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Contact_information;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $fillable = ["first-name", "last-name", "email", "password", "wallet"];


    public function services()
    {
        return $this->hasMany(Service::class, "customer_id");
    }

    public function coantactInformations()
    {
        return $this->hasMany(Contact_information::class, "customer_id");
    }
}
