<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "user_type", "likeable_id", "likeable_type"];
    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->morphTo();
    }



}
