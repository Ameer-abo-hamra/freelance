<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ["first-name","last-name"];




    public function services(){
        return $this->hasMany(Service::class,"service_id");
    }
}
