<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Comment_like extends Model
{
    use HasFactory;


    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, "comment_id");
    }
    // for testing conflicts from ameer
}
