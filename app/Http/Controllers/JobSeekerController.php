<?php

namespace App\Http\Controllers;

use App\Models\Job_seeker;
use App\Traits\Response;
use Validator;
use Illuminate\Http\Request;
use Hash;

class JobSeekerController extends Controller
{
    use Response;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "username" => "unique:job_seekers|required",
            "full_name" => "required",
            "password" => "required | min:8  | max:20",
            "birth_date" => "required"
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

        return $this->returnSuccess("your data is saved successfully ");
    }
}   
