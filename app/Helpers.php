<?php
use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;
use App\Models\Offer;


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
    $validator = Validator::make($request->all(), [
        "verificationCode" => "required",
    ]);
    if ($validator->fails()) {
        return ResponseTrait::returnError($validator->errors()->first());
    }
    if (Auth::guard($guard)->user()->verificationCode == $request->verificationCode) {
        auth($guard)->user()->update([
            "isActive" => true,
        ]);
        return ResponseTrait::returnSuccess("you have verfied your account successfully");
    }
    return ResponseTrait::returnError("your code is not equal to our code ");
}


function getAuth($guard)
{
    return Auth::guard($guard)->user();

}


function addOffer($request, $guard)
{
    $validation = Validator::make($request->all(), [
        "title" => "required",
        "body" => "required",
        "position" => "required",
        "skill_ids"=> "required",
    ]);
    if ($validation->fails()) {
        return ResponseTrait::returnError($validation->errors()->first());
    }

    $offer = Offer::create([
        "author" => Auth::guard($guard)->user()->name,
        "title" => $request->title,
        "body" => $request->body,
        "position" => $request->position,
        "company_id" => Auth::guard($guard)->user()->id,
    ]);
    $skill_ids = $request->skill_ids;
    if (!empty($skill_ids)) {

        foreach ($skill_ids as $s) {
            $offer->skills()->attach($s);
        }
    } else {
        return ResponseTrait::returnError("you have to enter skills");
    }
    return ResponseTrait::returnSuccess("your offer is saved");
}
