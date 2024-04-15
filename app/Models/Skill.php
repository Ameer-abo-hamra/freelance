<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = ["category", "type", "skill_name", "skill_id", "offer_id"];



    public function jobSeekers()
    {
        return $this->belongsToMany(Job_seeker::Class, "job_seekers_skills", "skill_id", "job_seeker_id");
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, "skills_services", "service_id", "skill_id");
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, "offer_id", "skill_id");
    }
}
