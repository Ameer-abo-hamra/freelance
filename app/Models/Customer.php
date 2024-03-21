<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact_information;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ["first-name", "last-name", "email", "password", "wallet"];




    public function services()
    {
        return $this->hasMany(Service::class, "service_id");
    }

    public function coantactInformations()
    {
        return $this->hasMany(Contact_information::class, "customer_id");
    }
}
