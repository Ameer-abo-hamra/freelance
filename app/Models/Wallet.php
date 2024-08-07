<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = ["balance","reserved","customer_id","company_id","job_seeker_id"];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function jobSeeker()
    {
        return $this->belongsTo(Job_seeker::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
