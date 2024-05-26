<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = ["reporter_id", "repoter_type", "reported_id", "reported_type", "reason"];
    public function reporter()
    {
        return $this->morphTo();
    }

    public function reported()
    {
        return $this->morphTo();
    }
}
