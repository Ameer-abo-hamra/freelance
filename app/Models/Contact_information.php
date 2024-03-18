<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact_information extends Model
{
    use HasFactory;
    protected $fillable = ['email', "phone", "address"];

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }
    public function jobSeeker()
    {
        return $this->belongsTo(Job_seeker::class, "job_seeker_id");
    }
}
