<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\ResponseTrait;
use Validator;
use Auth;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
            "employee_number" => "required |integer | min:10 | max:500000",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        Company::create([
            "name" => $request->name,
            "establishment_date" => $request->establishment_date,
            "employee_number" => $request->employee_number
        ]);
        return $this->returnSuccess("your account created successfully");
    }

    // public function login(Request $request){
    //     $validator = validator::make($request->all(), [
    //         "name" => "required|unique:companies| max:15",
    //         "employee_number" => "required |integer | min:10 | max:500000",
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->returnError($validator->errors()->first());
    //     }
    //     $credential=$request->only("name");
    //     $token=Auth::guard("web-company")->attempt($credential);
    //     if($token){
    //         $company=Auth::guard("web-company")->user();
    //         $company->api=$token;
    //         return $this->returnData("U R logged-in successfully","company data",$company);
    //     }
    //     return $this->returnError("your data is invalid .. enter it again");
    // }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
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
}
