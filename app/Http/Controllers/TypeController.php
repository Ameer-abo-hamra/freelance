<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Validator;
use App\Models\Type;

class TypeController extends Controller
{
    use ResponseTrait;
    public function addType(Request $request){
        $validator =  validator::make($request->all(),[
            "type_name" => "required|string",
            "category_id" => "required|exists:categories,id",
            "skill_ids" => "required|array",
            "skill_ids.*" => "exists:skills,id"
        ]);
        if($validator->fails()){
            return $this->returnError("your data is not completed");
        }
        $type=Type::create([
            "type_name" => $request->type_name,
            "category_id" => $request->category_id
        ]);
        $skill_ids=$request->skill_ids;
        foreach($skill_ids as $skill_id){
            $type->skills()->attach($skill_id);
        }
        return $this->returnSuccess("the type added successfully");
    }

    public function updateType(Request $request){
        Type::update([
            "type_name" => $request->type_name
        ]);
        return $this->returnSuccess("the type updated successfully");
    }

    public function deleteType($id){
        Type::find($id)->delete();
        return $this->returnSuccess("the type is deleted");
    }
}
