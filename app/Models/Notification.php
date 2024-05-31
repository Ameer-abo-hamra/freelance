<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
protected $fillable = ["content","notfiSender_type","notfiSender_id" , "notfiReciver_type" ,"notfiReciver_id"] ;

public function setNotfiSender_typeAttribute($value)
{
    $this->attributes['notfiSender_type'] = ucwords($value);
}

public function setNotfiReciver_typeAttribute($value)
{
    $this->attributes['notfiReciver_type'] = ucwords($value);
}
    public function notfiSender() {

        return $this->morphTo();
    }


    public function notfiReciver() {

        return $this->morphTo();
    }
}
