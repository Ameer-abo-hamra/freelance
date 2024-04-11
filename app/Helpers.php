<?php
use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;

class Re
{
    use ResponseTrait;
}

function makeCode($type, $email)
{
    $code = Str::random(6);
    if ($type == "customer") {
        Mail::to($email)->send(new Customer($code));
        return $code;

    } elseif ($type == "company") {
        Mail::to($email)->send(new Company($code));
        return $code;

    } else {
        Mail::to($email)->send(new JobseekerMail($code));
        return $code;
    }
}

function verify($request, $guard)
{
    $re = new Re();
    $validator = Validator::make($request->all(), [
        "verificationCode" => "required",
    ]);
    if ($validator->fails()) {
        return $re->returnError($validator->errors()->first());
    }
    if (Auth::guard($guard)->user()->verificationCode == $request->verificationCode) {
        auth($guard)->user()->update([
            "isActive" => true,
        ]);
        return $re->returnSuccess("you have verfied your account successfully");
    }
    return $re->returnError("your code is not equal to our code ");
}


function getAuth($guard)
{
    return Auth::guard($guard)->user();

}
