<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Customer;
use App\Models\Contact_information;
class CustomerController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            "first-name" => "min:3||max:10",
            "last-name" => "min:3||max:10"
        ]);
        if($validator->fails()){
            // return response()->json([
            //     "status" => false,
            //     "message" => "enter your data again"
            // ]);
        }
        else{
            Customer::create([
                "first-name" => $request["first-name"],
                "last-name" => $request["last-name"],
                "email" => $request["email"],
                "password" => $request["password"]
            ]);
        }
    }
}
