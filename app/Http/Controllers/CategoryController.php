<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Traits\ResponseTrait;
use App\Models\Category;
use App\Models\Type;
use App\Models\Skill;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function addCategory(Request $request)
    {
        $validator = validator::make($request->all(), [
            "category_name" => "required",
            "type_ids" => "array",
            "type_ids.*" => "exists:types,id",
            "skill_ids" => "array",
            "skill_ids.*" => "exists:skills,id"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $category = category::create([
            "category_name" => $request->category_name
        ]);
        $type_ids = $request->type_ids;
        if ($type_ids) {
            $types = Type::find($type_ids); // Fetching the Type models
            $category->types()->saveMany($types);
        }


        $skill_ids = $request->skill_ids;
        if ($skill_ids) {
            $skills = Skill::find($skill_ids); // Fetching the Skill models
            $category->skills()->saveMany($skills);
        }
        return $this->returnSuccess("the category added successfully");
    }

    public function updateCategory(Request $request)
    {
        $category_id = $request->category_id;
        Category::find($category_id)->update([
            "category_name" => $request->category_name
        ]);
        return $this->returnSuccess("the category is updated");
    }

    public function deleteCategory($id)
    {
        $category = category::find($id);
        if ($category) {
            $category->delete();
            return $this->returnSuccess("category deleted successfully");
        }
        return $this->returnError("category not found");
    }
}
