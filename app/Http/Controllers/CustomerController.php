<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Customer;
use App\Models\Contact_information;
use App\Traits\Response;
use Hash;

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
                "password" => Hash::make($request["password"]),
            ]);
        }
    }
}
