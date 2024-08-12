<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ["skill_name", "category_id"];

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, "skills_types", "skill_id", "type_id");
    }

    public function job_seekers()
    {
        return $this->belongsToMany(Job_seeker::class, "job_seekers_skills", "skill_id", "job_seeker_id");
    }
}
