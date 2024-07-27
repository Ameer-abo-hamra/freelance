<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
// use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\Skill;
use Validator;

class SkillController extends Controller
{
    use ResponseTrait;
    public function addSkill(Request $request)
    {
        $validator = validator::make($request->all(), [
            "skill_name" => "required|string",
            "category_id" => "required|exists:categories,id",
            "type_ids" => "required|array",
            "type_ids.*" => "exists:types,id"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $skill = Skill::create([
            "skill_name" => $request->skill_name,
            "category_id" => $request->category_id
        ]);
        $type_ids = $request->type_ids;
        foreach ($type_ids as $type_id) {
            $skill->types()->attach($type_id);
        }
        return $this->returnSuccess("the skill is added successfully");
    }

    public function updateSkill(Request $request)
    {
        $skill_id=$request->skill_id;
        Skill::find($skill_id)->update([
            "skill_name" => $request->skill_name
        ]);
        return $this->returnSuccess("the skill is updated");
    }

    public function deleteSkill($id)
    {
        Skill::find($id)->delete();
        return $this->returnSuccess("the skill deleted successfully");
    }
}
