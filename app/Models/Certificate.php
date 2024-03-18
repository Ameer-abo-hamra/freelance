<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = ["certificate_name", "graduation_date", "rate"];
    public function jobSeeker()
    {
        return $this->belongsTo(Job_seeker::class, "job_seeker_id");
    }
}
