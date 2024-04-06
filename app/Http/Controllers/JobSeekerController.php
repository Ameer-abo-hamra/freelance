<?php

namespace App\Http\Controllers;

use App\Models\Job_seeker;
use App\Traits\ResponseTrait;
use Validator;
use Illuminate\Http\Request;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
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
        $job_seeker=Job_seeker::create([
            "username" => $request->username,
            "full_name" => $request->full_name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "birth_date" => $request->birth_date,
            "verificationCode" => makeCode("job_seeker", $request->email),
        ]);
        Auth::guard("web-job_seeker")->login($job_seeker);
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
        $credential = $request->only("username","password");

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
        $token=Auth::guard("api-job_seeker")->attempt($credential);
        if($token){
            $job_seeker=Auth::guard("api-job_seeker")->user();
            $job_seeker->api=$token;
            return $this->returnData("U R logged-in successfully","job_seeker data",$job_seeker);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function progress(Request $request){
        $job_seeker = Auth::guard("web-job_seeker")->user();
        // return $job_seeker;
        $job_seeker->offers()->attach($request->offer_id);
    }

    public function logout_api(Request $request){
        $token=$request->bearerToken();
        try {
            JWTAuth::setToken($token)->invalidate();
            return $this->returnSuccess("U R logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }
    }

    public function logout(){
        Auth::guard("web-job_seeker")->logout();
        return $this->returnSuccess("U R logged-out successfully");
    }
}
