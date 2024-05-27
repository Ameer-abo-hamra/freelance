<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = ["type_name","category_id"];

    public function category(){
        return $this->belongsTo(Category::class,"category_id");
    }

    public function skills(){
        return $this->belongsToMany(Skill::class,"skills_types","type_id","skill_id");
    }
}
