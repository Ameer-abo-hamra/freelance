<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Offer;
use App\Traits\Response;
use Validator;
use Auth;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use Response;
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

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            "name" => "max:15||required",
            "establishment_date" => "required",
            "employee_number" => "required"
        ]);
        if($validator->fails()){
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("name");
        if(Auth::attempt($credential)){
            return view("hadeel");
        }
        return $this->returnError("enter your information again");
    }


    // public function addOffer(Request $request){
    //     Offer::create([
    //         "title" => $request->title,
    //         "body" => $request->body,
    //         "position" => $request->position,
    //         "company_id" => $request->company_id
    //     ]);
    //     return $this->returnSuccess("your offer published successfully");
    // }
}
