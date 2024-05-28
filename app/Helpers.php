<?php
use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use App\Models\Job_seeker;
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
        "skill_ids" => "required",
    ]);
    if ($validation->fails()) {
        return ResponseTrait::returnError($validation->errors()->first());
    }

    $offer = Offer::create([
        "author" => Auth::guard($guard)->user()->name,
        "title" => $request->title,
        "body" => $request->body,
        "position" => $request->position,
        "type" => $request->type,
        "details" => $request->details,
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


function category()
{

    return [
        "programming",
        "architecture",
        "financial",
    ];
}

function getCategoryApi($guard)
{
    $job_seeker = Auth::guard($guard)->user();
    $skills = $job_seeker->skills();
    return $skills;
    // $type =$skills->type;
    // return $job_seeker_id;
    // $job_seeker_id=11;
    // $job_seeker=Job_seeker::where("id",$job_seeker_id)->get();
    // return $job_seeker->skills();
    // return $skills;
    // return $job_seeker;
}


function browse($type, $id)
{

    $user = '';

    if ($type = "company") {

        $user = \App\Models\Company::find($id);

    } elseif ($type = "job_seeker") {
        $user = Job_seeker::find($id);

    } elseif ($type = "customer") {
        $user = \App\Models\Customer::find($id);
    }

    $posts = [];
    foreach ($user->followMade as $f) {

        if (class_exists($f->followReciver_type)) {
            array_push($posts, $f->followReciver_type::find($f->followReciver_id)->posts);
        } else {
            return ResponseTrait::returnError("this class does not exist");

        }
    }
    return ResponseTrait::returnData("", "posts", $posts);
}

function putFollow($followMakerType, $followMakerid, $followReciverType, $followReciverid)
{
    $followMaker = '';
    $follwReciver = '';
    if ($followMakerType == "company") {

        $followMaker = \App\Models\Company::find($followMakerid);

    } elseif ($followMakerType == "job_seeker") {
        $followMaker = Job_seeker::find($followMakerid);

    } elseif ($followMakerType == "customer") {
        $followMaker = \App\Models\Customer::find($followMakerid);
    } else {
        return ResponseTrait::returnError("check the followMakerType or followMakerid ");
    }
    if ($followReciverType == "company") {

        $follwReciver = " App\Models\Company";

    } elseif ($followReciverType == "job_seeker") {
        $follwReciver = "App\Models\Job_seeker";

    } elseif ($followReciverType == "customer") {
        $follwReciver = "App\Models\Customer";
    } else {
        return ResponseTrait::returnError("check the followReciverType");
    }
    $followMaker->followMade()->create([

        "followReciver_type" => $follwReciver,
        "followReciver_id" => $followReciverid,
    ]);

    return ResponseTrait::returnSuccess("done");
}
