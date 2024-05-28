<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceApply extends Model
{
    use HasFactory;

    public function applyable(){
        return $this->morphTo();
    }

    public function services(){
        return $this->belongsToMany(Service::class,"services_applied","serviceApplied_id","service_id");
    }
}
