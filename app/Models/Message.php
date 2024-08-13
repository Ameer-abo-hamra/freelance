<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ["reciver_type", "reciver_id", "sender_type", "sender_id", "content","created_at","updated_at"];

    public function sender()
    {
        return $this->morphTo();
    }
    public function reciver()
    {
        return $this->morphTo();
    }

}
