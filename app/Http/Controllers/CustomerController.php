<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Contact_information;
use App\Traits\ResponseTrait;
use Auth;
use Hash;
use App\Mail\testmail;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Helpers;

class CustomerController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "usrename" => "min:3||max:10",
            "full_name" => "min:3||max:20",
            "email" => "required | email |unique:customers",
            "password" => "required |min:8 | max:20"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        } else {
            $customer = Customer::create([
                "username" => $request["username"],
                "full_name" => $request["full_name"],
                "email" => $request["email"],
                "password" => Hash::make($request["password"]),
                "birth_date" => $request->birth_date,
                "verificationCode" => makeCode("customer", $request->email),
            ]);
            Auth::guard('customer')->login($customer);
            return $this->returnSuccess("your account created successfully");
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required||email",
            "password" => "required||min:8||max:20"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");
        try {
            if (auth()->guard("customer")->attempt($credential)) {
                $customer = auth::guard("customer")->user();


                return $this->returnData("U r logged-in successully", "customer data", $customer);
            }
        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function login_api(Request $request)
    {
        $validator = validator::make($request->all(), [
            "email" => "required | email ",
            "password" => "required | max:15 | min:6"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");
        $token = Auth::guard("api-customer")->attempt($credential);
        if ($token) {
            $customer = Auth::guard("api-customer")->user();
            $customer->api = $token;
            return $this->returnData("U R logged-in successfully", "customer data", $customer);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }





    public function logout()
    {
        Auth::guard("customer")->logout();
        return $this->returnSuccess("you are logged out successfully");
    }

    public function verify(Request $request)
    {
        if (Auth::guard("customer")->user()->verificationCode == $request->verificationCode) {
            auth("customer")->user()->update([
                "isActive" => true,
            ]);
            return $this->returnSuccess("you have verfied your account successfully");
        }
        return $this->returnError("your code is not equal to our code ");
    }

    public function logout_api(Request $request){
        $token=$request->bearerToken();
        // $token=$request->header("Authoriaztion");
        // return $token;
        try {
            JWTAuth::setToken($token)->invalidate();
            return $this->returnSuccess("U R logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }
    }
}
