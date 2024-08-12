<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ["description", "customer_id", "serviceable_id", "serviceable_type", "requestable_id", "requestable_type","is_accepted",
        "state","price"];



    // public function appliers(){
    //     return $this->belongsToMany(ServiceApply::class,"services_applied","service_id","serviceApplied_id");
    // }
    public function appliers() {
        return $this->hasMany(ServiceApply::class , "service_id");
    }
    // public function requester()
    // {
    //     return $this->morphTo();
    // }
    public function customer(){
        return $this->belongsTo(Customer::class,"customer_id");
    }


    public function skills()
    {
        return $this->belongsToMany(Skill::class, "skills_services", "service_id", "skill_id");
    }
}

