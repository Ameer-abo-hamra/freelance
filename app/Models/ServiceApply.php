<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'applyable_type',
        'applyable_id',
        'service_id',
        'offer',
        'isAccepted',
    ];
    public function applyable()
    {
        return $this->morphTo();
    }

    public function services()
    {
        return $this->belongsTo(Service::class, "service_id");
    }

    
}
