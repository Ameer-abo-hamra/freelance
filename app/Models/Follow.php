<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    protected $fillable = ["followMaker_type","followMaker_id" , "followReciver_type","followReciver_id"];
    public function setFollowMakerTypeAttribute($value)
    {
        $this->attributes['followMaker_type'] = ucwords($value);
    }

    public function setFollowReciverTypeAttribute($value)
    {
        $this->attributes['followReciver_type'] = ucwords($value);
    }
    public function followMaker() {
        return $this->morphTo();
    }


    public function followReciver() {
        return $this->morphTo();
    }

}
