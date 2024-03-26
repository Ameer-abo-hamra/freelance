<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\ResponseTrait;
use Validator;
use Auth;
use Hash;
use App\Models\Skill;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
            "password" => "required",
            "employee_number" => "required |integer | min:10 | max:500000",
            "establishment_date" => "required | date"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        Company::create([
            "name" => $request->name,
            "password" => Hash::make($request->password),
            "establishment_date" => $request->establishment_date,
            "employee_number" => $request->employee_number
        ]);
        return $this->returnSuccess("your account created successfully");
    }

    public function login_api(Request $request){
        $validator = validator::make($request->all(), [
            "name" => "required| max:15",
            "password" => "required",
            "employee_number" => "required |integer | min:10 | max:500000",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential=$request->only("name","password");
        $token=Auth::guard("api-company")->attempt($credential);
        if($token){
            $company=Auth::guard("api-company")->user();
            $company->api=$token;
            return $this->returnData("U R logged-in successfully","company data",$company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required| max:15",
            "employee_number" => "required |integer | min:10 | max:500000",
        ]);
        if($validator->fails()){
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("name");

        if (Auth::guard("web-company")->attempt($credential)) {
            $company = Auth::guard("web-company")->user();
            return $this->returnData("U R logged-in successfully", "company data", $company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function getCategory(){
        $categories = Skill::distinct()->get("category")->groupBy("skill categoriess");
        return $categories;
    }

    public function getTypesSkills(Request $request){
        $types = Skill::where("category",$request->category)->distinct()->get("type");
        return $types;
    }

    public function getSkillName(Request $request){
        $skills = Skill::where("type",$request->type)->get("skill_name");
        return $skills;
    }
}
