<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ["description"];
    public function job_seeker(){
        return $this->belongsTo(Job_seeker::class,"job_seeker_id");
    }

    public function customer(){
        return $this->belongsTo(Customer::class,"customer_id");
    }

    public function company(){
        return $this->belongsTo(Company::class,"company_id");
    }

    public function skills(){
        return $this->belongsToMany(Skill::class,"skills_services","skill_id","service_id");
    }
}
