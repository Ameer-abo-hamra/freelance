<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact_information extends Model
{
    use HasFactory;
    protected $fillable = ['email', "phone", "address", "contactable_id", "contactable_type"];

    public function contactable()
    {
        return $this->morphTo();
    }
}
