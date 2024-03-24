<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Contact_information;
use App\Traits\Response;
use Auth;
use Hash;
use App\Mail\testmail;

class CustomerController extends Controller
{
    use Response;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first-name" => "min:3||max:10",
            "last-name" => "min:3||max:10",
            "email" => "required | email |unique:customers",
            "password" => "required |min:8 | max:20"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        } else {
            Customer::create([
                "first-name" => $request["first-name"],
                "last-name" => $request["last-name"],
                "email" => $request["email"],
                "password" => Hash::make($request["password"])
                // "password" => bcrypt($request->password),
            ]);
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
                Mail::to("ameerabohamra314017@gmail.com")->send(new testmail());

                return $this->returnData("U r logged-in successully", "customer data", $customer);
            }
        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
        }
        return $this->returnError("your data is invalid .. enter it again");
    }
}
