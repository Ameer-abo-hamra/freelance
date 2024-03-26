<?php

namespace App\Http\Controllers;

use App\Models\Job_seeker;
use App\Traits\ResponseTrait;
use Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;


class JobSeekerController extends Controller
{

    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "username" => "unique:job_seekers||required||min:5||max:10",
            "full_name" => "required",
            "password" => "required||unique:job_seekers",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        Job_seeker::create([
            "username" => $request->username,
            "full_name" => $request->full_name,
            "password" => Hash::make($request->password),
            "birth_date" => $request->birth_date
        ]);
        return $this->returnSuccess("your account created successfully");
    }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "username" => "required||min:5||max:10",
            "password" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("password");

        if (Auth::guard("web-job_seeker")->attempt($credential)) {
            $job_seeker = Auth::guard("web-job_seeker")->user();
            return $this->returnData("U R logged-in successfully", "job_seeker data", $job_seeker);
        }
        return $this->returnError("your data is invalid .. please enter it again");
    }

    public function login_api(Request $request){
        $validator = validator::make($request->all(), [
            "username" => "required||min:5||max:10",
            "password" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential=$request->only("username","password");
        // return $credential;
        $token=Auth::guard("api-job_seeker")->attempt($credential);
        // return $token;
        if($token){
            $job_seeker=Auth::guard("api-job_seeker")->user();
            $job_seeker->api=$token;
            return $this->returnData("U R logged-in successfully","job_seeker data",$job_seeker);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }


}
