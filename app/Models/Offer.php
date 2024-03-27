<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = ["title", "body", "position", "author", "company_id", "skill_id", "offer_id"];

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function jobSeekers()
    {
        return $this->belongsToMany(Job_seeker::class, "job_seekers_offers", "offer_id", "job_seeker_id");
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, "skills_offers", "offer_id", "skill_id");
    }
}
