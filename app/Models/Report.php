<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = ["reason","reported_type","reported_id"];
    public function reporter()
    {
        return $this->morphTo();
    }
    public function reported()
    {
        return $this->morphTo();
    }


}
