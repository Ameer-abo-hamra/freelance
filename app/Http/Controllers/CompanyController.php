<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Offer;
use App\Traits\ResponseTrait;
use PHPUnit\Framework\Constraint\IsEmpty;
use Validator;
use Auth;
use Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Skill;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            "establishment_date" => "required | date",
            "email" => "required | unique:companies| email",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $company = Company::create([
            "name" => $request->name,
            "password" => Hash::make($request->password),
            "establishment_date" => $request->establishment_date,
            "employee_number" => $request->employee_number,
            "email" => $request->email,
            // "verificationCode" => makeCode("company", $request->email),
        ]);
        Auth::guard("web-company")->login($company);
        $credential = $request->only("name", "password");
        Auth::guard("api-company")->attempt($credential);
        Company::where("name", $request->name)->update([
            "verificationCode" => makeCode("company", $request->email),
        ]);
        return $this->returnSuccess("your account created successfully");
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "verificationCode" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        if (Auth::guard("web-company")->user()->verificationCode == $request->verificationCode) {
            auth("web-company")->user()->update([
                "isActive" => true,
            ]);
            return $this->returnSuccess("you have verfied your account successfully");
        }
        return $this->returnError("your code is not equal to our code ");
    }
    public function apiVerify(Request $request)
    {
        return verify($request, "api-company");

    }
    public function resend()
    {
        $user = Auth::guard("web-company")->user();
        $user->update([
            "verificationCode" => makeCode("company", $user->email)
        ]);
        return $this->returnSuccess("check your email please :)");
    }

    public function login_api(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required| max:15",
            "password" => "required",
            "employee_number" => "required |integer | min:10 | max:500000",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("name", "password");
        $token = Auth::guard("api-company")->attempt($credential);
        if ($token) {
            $company = Auth::guard("api-company")->user();
            $company->api = $token;
            return $this->returnData("U R logged-in successfully", "company data", $company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    // public function logout_api(Request $request)
    // {
    //     $token = $request->bearerToken();
    //     try {
    //         JWTAuth::setToken($token)->invalidate();
    //         return $this->returnSuccess("U R logged-out successfully");
    //     } catch (JWTException $e) {
    //         return $this->returnError("there were smth wrong");
    //     }
    // }
    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required| max:15",
            "password" => "required | min:8| max:20",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $credential = $request->only("name", "password");


        if (Auth::guard("web-company")->attempt($credential)) {
            $company = Auth::guard("web-company")->user();
            return $this->returnData("U R logged-in successfully", "company data", $company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function logout()
    {
        Auth::guard("web-company")->logout();
        return $this->returnSuccess("you are logged-out successfully");
    }
    public function logout_api(Request $request)
    {

        try {
            auth("api-company")->logout();
            return $this->returnSuccess("you are logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }

    }


    public function getCategory()
    {
        $categories = Skill::distinct()->get("category");
        $arr = [];
        foreach ($categories as $cat) {
            array_push($arr, $cat["category"]);
        }
        return $this->returnData("", "categories", $arr);
    }

    public function getTypesSkills($category)
    {
        $types = Skill::where("category", $category)->distinct()->get("type");
        $arr = [];
        foreach ($types as $cat) {
            array_push($arr, $cat["type"]);
        }
        return $this->returnData("", "types", $arr);
    }


    public function getSkillName($type)
    {
        $skills = Skill::where("type", $type)->get("skill_name");
        $arr = [];
        foreach ($skills as $cat) {
            array_push($arr, $cat["skill_name"]);
        }
        return $this->returnData("", "skill_names", $arr);

    }

    public function addOffer(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "title" => "required",
            "body" => "required"
        ]);
        if ($validation->fails()) {
            return $this->returnError($validation->errors()->first());
        }

        $offer = Offer::create([
            "author" => Auth::guard("web-company")->user()->name,
            "title" => $request->title,
            "body" => $request->body,
            "position" => $request->position,
            "company_id" => Auth::guard("web-company")->user()->id,
        ]);
        $skill_ids = $request->skill_ids;
        if (!empty($skill_ids)) {

            foreach ($skill_ids as $s) {
                $offer->skills()->attach($s);
            }
        } else {
            return $this->returnError("you have to enter skills");
        }
        return $this->returnSuccess("your offer is saved");
    }

    public function log_out()
    {
        Auth::guard("web-company")->logout();
        return $this->returnSuccess("U R logged-out successfully");
    }
}
