<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    public function job_seekers(){
        return $this->hasMany(Job_seeker::class,"job_seeker_id");
    }

    public function companies(){
        return $this->hasMany(Company::class,"company_id");
    }

    public function customers(){
        return $this->hasMany(Customer::class,"customer_id");
    }
}
