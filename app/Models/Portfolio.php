<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    public function job_seeker(){
        return $this->belongsTo(Job_seeker::class,"job_seeker_id");
    }

    public function company(){
        return $this->belongsTo(Company::class,"company_id");
    }
}

