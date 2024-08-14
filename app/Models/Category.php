<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ["category_name"];
    protected $hidden = ["created_at","updated_at"];

    public function skills(){
        return $this->hasMany(Skill::class,"category_id");
    }

    public function types(){
        return $this->hasMany(Type::class,"category_id");
    }
}
