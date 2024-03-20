<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Customer;
use App\Models\Contact_information;
use App\Traits\Response;
use Auth;
use Hash;
class CustomerController extends Controller
{
    use Response;
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            "first-name" => "min:3||max:10",
            "last-name" => "min:3||max:10",
            "email" => "unique:customers||required||email",
            "password" => "unique:customers||required||min:8||max:20"
        ]);
        if($validator->fails()){
            return $this->returnError($validator->errors()->first());
        }
        else{
            Customer::create([
                "first-name" => $request["first-name"],
                "last-name" => $request["last-name"],
                "email" => $request["email"],
                "password" => Hash::make($request["password"])
            ]);
            return $this->returnSuccess("your account created successfully");
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            "first-name" => "min:3||max:10",
            "last-name" => "min:3||max:10",
            "email" => "unique||required||email",
            "password" => "unique||required||min:8||max:20"
        ]);
        if($validator->fails()){
            return $this->returnError($validator->errors()->first());
        }
        $credential=$request->only("email","password");
        $token=Auth::guard("web-customer")->attempt($credential);
        if($token){
            $customer=auth::guard("web-customer")->user();
            $customer->api=$token;
            return $this->returnData("U r logged-in successully","customer data",$customer);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }
}
