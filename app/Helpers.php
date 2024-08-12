<?php
use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use App\Models\Job_seeker;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;
use App\Models\Offer;
use App\Events\RespondApplicants;
use App\Models\Like;
use App\Events\Notifications;
use App\Models\Notification;
use Illuminate\Http\Request;

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
    // return ResponseTrait::returnData("","",getAllFollowRecived(Auth::guard($guard)->user()));

    $skill_ids = $request->skill_ids;
    if (!empty($skill_ids)) {

        foreach ($skill_ids as $s) {
            $offer->skills()->attach($s);
        }
    } else {
        return ResponseTrait::returnError("you have to enter skills");
    }

    $follwers = getAllFollowRecived(Auth::guard($guard)->user());
    return ResponseTrait::returnData("","",$follwers);
    // return ResponseTrait::returnSuccess("your offer is saved");
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

function addComment($commentMaker_type, $commentMaker_id, $post_id, $title, $body)
{

    if ($commentMaker_type == "company") {

        $commentMaker = \App\Models\Company::find($commentMaker_id);

    } elseif ($commentMaker_type == "job_seeker") {
        $commentMaker = Job_seeker::find($commentMaker_id);

    } elseif ($commentMaker_type == "customer") {
        $commentMaker = \App\Models\Customer::find($commentMaker_id);
    } else {
        return ResponseTrait::returnError("check the followMakerType or followMakerid ");
    }

    $commentMaker->comments()->create([
        "post_id" => $post_id,
        "body" => $body,
        "title" => $title,
    ]);
    return ResponseTrait::returnSuccess("done");
}


function ChangeOfferState($request, $guard)
{

    $company = getAuth($guard);

    foreach ($company->offers as $of) {
        if ($of->id == $request->offer_id) {
            if ($offer = Offer::findOrFail($request->offer_id)) {
                foreach ($offer->jobSeekers as $jobseeker) {
                    if ($jobseeker->id == $request->job_seeker_id) {
                        $offer->jobSeekers()->update(
                            [
                                "isAccepted" => $request->state
                            ]
                        );
                        $content = '';
                        if ($request->state) {
                            $content = "Your employment application has been accepted by " . getAuth("web-company")->name;
                            broadcast(new Notifications($content, "jobseeker", $jobseeker->id));
                            fillNotification("company", $company->id, "jobseeker", $request->job_seeker_id, $content);
                        } else {
                            $content = "Your employment application has been rejected by " . getAuth("web-company")->name;
                            broadcast(new Notifications($content, "jobseeker", $jobseeker->id));
                            fillNotification("company", $company->id, "jobseeker", $request->job_seeker_id, $content);

                        }

                        getAuth($guard)->notificationSent()->create([
                            "notfiReciver_type" => "app\Models\Job_seeker",
                            "notfiReciver_id" => $request->job_seeker_id,
                            "content" => $content
                        ]);
                        return ResponseTrait::returnSuccess("this order is changed ");
                    }
                }
                return ResponseTrait::returnError("this jobSeeker did not apply for this offer");
            }
        }
    }
    return ResponseTrait::returnError("you do not have a permission to change this employment aplicant");

}

function addLike($request, $guard, $likeableType)
{
    $validator = Validator::make($request->all(), [
        $likeableType . '_id' => 'required|integer|exists:' . str::plural($likeableType) . ',id'
    ]);

    if ($validator->fails()) {
        return ResponseTrait::returnError($validator->errors()->first());
    }

    $likeableId = $request->input($likeableType . '_id');
    $likeableClass = 'App\\Models\\' . ucfirst($likeableType);
    $likeable = $likeableClass::find($likeableId);

    $user = auth()->guard($guard)->user();


    if (!$user) {
        return ResponseTrait::returnError("invalid user");
    }

    $existingLike = Like::where('likeable_id', $likeable->id)
        ->where('likeable_type', $likeableClass)
        ->where('user_id', $user->id)
        ->where('user_type', get_class($user))
        ->first();

    if ($existingLike) {
        return ResponseTrait::returnError("User has already liked this " . $likeableType);
    }
    // return ResponseTrait::returnData("","",$owner);
    $like = new Like();
    $like->user()->associate($user);
    $like->likeable()->associate($likeable);
    $like->save();
    $channel_name = class_basename($likeable->postable);
    $name = $user->username;
    if (!$name) {
        $name = $user->name;
    }

    $owner = $likeableType . 'able';
    broadcast(new Notifications($name . " reacted to your " . $likeableType, $channel_name, $likeable->$owner->id))->toOthers();
    fillNotification(class_basename($user), $user->id, class_basename($likeable->postable), $likeable->$owner->id, $name . " reacted to your " . $likeableType);
    return ResponseTrait::returnSuccess(ucfirst($likeableType) . " liked successfully");
}

function removeLike($request, $guard, $likeableType)
{
    $validator = Validator::make($request->all(), [
        $likeableType . '_id' => 'required|integer|exists:' . Str::plural($likeableType) . ',id'
    ]);

    if ($validator->fails()) {
        return ResponseTrait::returnError($validator->errors()->first());
    }

    $likeableId = $request->input($likeableType . '_id');
    $likeableClass = 'App\\Models\\' . ucfirst($likeableType);
    $likeable = $likeableClass::find($likeableId);

    $user = auth()->guard($guard)->user();

    if (!$user) {
        return ResponseTrait::returnError("invalid user");
    }

    $like = Like::where('likeable_id', $likeable->id)
        ->where('likeable_type', $likeableClass)
        ->where('user_id', $user->id)
        ->where('user_type', get_class($user))
        ->first();

    if (!$like) {
        return ResponseTrait::returnError("like not found");
    }

    $like->delete();

    return ResponseTrait::returnSuccess(ucfirst($likeableType) . " unliked successfully");
}

function fillNotification($senderType, $senderId, $reciverType, $reciverId, $content)
{

    $sender = '';
    $reciver = '';

    if ($senderType == "customer" || $senderType == "Customer") {

        $sender = App\Models\Customer::find($senderId);
    } elseif ($senderType == "jobseeker" || $senderType == "Job_seeker") {
        $sender = App\Models\Job_seeker::find($senderId);
    } else {
        $sender = App\Models\Company::find($senderId);
    }
    if ($reciverType == "customer" || $reciverType == "Customer") {

        $reciver = 'App\Models\Customer';
    } elseif ($reciverType == "jobseeker" || $reciverType == "Job_seeker") {
        $reciver = "App\Models\Jobseeker";
    } else {
        $reciver = 'App\Models\Company';
    }

    $sender->notificationSent()->create([

        "notfiReciver_id" => $reciverId,
        "notfiReciver_type" => $reciver,
        "content" => $content

    ]);



}


function photo(Request $request, $diskName, $folderName, $id)
{

    $name = $id . $request->file("file")->getClientOriginalName();
    $path = $request->file("file")->storeAs($folderName, $name, $diskName);
    return $path;

}

function getAllFollowRecived($user)
{
    $followers = Job_seeker::find($user->id)->followRecived;
    return $followers;
}


;
