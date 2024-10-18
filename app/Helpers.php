<?php

use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use App\Models\Follow;
use App\Models\Job_seeker;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Offer;
use App\Events\RespondApplicants;
use App\Models\Like;
use App\Events\Notifications;
use App\Models\ServiceApply;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Message;

class Inter
{
    use ResponseTrait;
}
function makeInst()
{
    return  new Inter();
}
function verify($request, $guard)
{

    $validator = Validator::make($request->all(), [
        "verificationCode" => "required",
    ]);
    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }
    if (Auth::guard($guard)->user()->verificationCode == $request->verificationCode) {

        auth($guard)->user()->update([
            "isActive" => true,
        ]);
        return makeInst() -> returnSuccess("you have verfied your account successfully");
    }
    return makeInst() -> returnError("your code is not equal to our code ");
}
function makeCode($type, $email): mixed
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



function getAuth($guard): ?\Illuminate\Contracts\Auth\Authenticatable
{
    return Auth::guard($guard)->user();

}

function addOffer($request, $guard): \Illuminate\Http\JsonResponse
{

    $validation = Validator::make($request->all(), [
        "title" => "required",
        "body" => "required",
        "position" => "required",
        "skill_ids" => "required",
    ]);
    if ($validation->fails()) {
        return makeInst()->returnError($validation->errors()->first());
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
    // return makeInst() -> returnData("","",getAllFollowRecived(Auth::guard($guard)->user()));

    $skill_ids = $request->skill_ids;
    if (!empty($skill_ids)) {

        foreach ($skill_ids as $s) {
            $offer->skills()->attach($s);
        }
    } else {
        return makeInst()->returnError("you have to enter skills");
    }

    $follwers_ids = getFollowRecivedJobSeekers(Auth::guard($guard)->user());
    foreach ($follwers_ids as $f) {
        broadcast(new Notifications(Auth::guard($guard)->user()->name . " Company has posted a job opportunity that you may be interested in", "job_seeker", $f->id));
        fillNotification("company", Auth::guard($guard)->user()->id, "jobseeker", $f->id, Auth::guard($guard)->user()->name . " Company has posted a job opportunity that you may be interested in");
    }

    // return makeInst() -> returnData("", "", $follwers_ids);
    return makeInst()->returnSuccess("your offer is saved");
}

function browse($type, $id): \Illuminate\Http\JsonResponse
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
            return makeInst()->returnError("this class does not exist");
        }
    }
    return makeInst()->returnData("", "posts", $posts);
}

function putFollow($followMakerType, $followMakerid, $followReceiverType, $followReceiverid): \Illuminate\Http\JsonResponse
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
        return makeInst()->returnError("check the followMakerType or followMakerid ");
    }
    if ($followReceiverType == "company") {

        $follwReciver = " App\Models\Company";

    } elseif ($followReceiverType == "job_seeker") {
        $follwReciver = "App\Models\Job_seeker";

    } elseif ($followReceiverType == "customer") {
        $follwReciver = "App\Models\Customer";
    } else {
        return makeInst()->returnError("check the followReciverType");
    }
    $followMaker->followMade()->create([

        "followReciver_type" => $follwReciver,
        "followReciver_id" => $followReceiverid,
    ]);

    return makeInst()->returnSuccess("done");
}

function addComment($commentMaker_type, $commentMaker_id, $post_id, $title, $body): \Illuminate\Http\JsonResponse
{


    if ($commentMaker_type == "company") {

        $commentMaker = \App\Models\Company::find($commentMaker_id);

    } elseif ($commentMaker_type == "job_seeker") {
        $commentMaker = Job_seeker::find($commentMaker_id);

    } elseif ($commentMaker_type == "customer") {
        $commentMaker = \App\Models\Customer::find($commentMaker_id);
    } else {
        return makeInst()->returnError("check the followMakerType or followMakerid ");
    }

    $commentMaker->comments()->create([
        "post_id" => $post_id,
        "body" => $body,
        "title" => $title,
    ]);
    return makeInst()->returnSuccess("done");
}

function ChangeOfferState($request, $guard): \Illuminate\Http\JsonResponse
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
                            $content = "Your employment application has been accepted by " . getAuth($guard)->name;
                            broadcast(new Notifications($content, "jobseeker", $jobseeker->id));
                            fillNotification("company", $company->id, "jobseeker", $request->job_seeker_id, $content);
                        } else {
                            $content = "Your employment application has been rejected by " . getAuth($guard)->name;
                            broadcast(new Notifications($content, "jobseeker", $jobseeker->id));
                            fillNotification("company", $company->id, "jobseeker", $request->job_seeker_id, $content);

                        }

                        getAuth($guard)->notificationSent()->create([
                            "notfiReciver_type" => "app\Models\Job_seeker",
                            "notfiReciver_id" => $request->job_seeker_id,
                            "content" => $content
                        ]);
                        return makeInst()->returnSuccess("this order is changed ");
                    }
                }
                return makeInst()->returnError("this jobSeeker did not apply for this offer");
            }
        }
    }
    return makeInst()->returnError("you do not have a permission to change this employment aplicant");

}

function addLike($request, $guard, $likeableType): \Illuminate\Http\JsonResponse
{

    $validator = Validator::make($request->all(), [
        $likeableType . '_id' => 'required|integer|exists:' . str::plural($likeableType) . ',id'
    ]);

    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }

    $likeableId = $request->input($likeableType . '_id');
    $likeableClass = 'App\\Models\\' . ucfirst($likeableType);
    $likeable = $likeableClass::find($likeableId);

    $user = auth()->guard($guard)->user();


    if (!$user) {
        return makeInst()->returnError("invalid user");
    }

    $existingLike = Like::where('likeable_id', $likeable->id)
        ->where('likeable_type', $likeableClass)
        ->where('user_id', $user->id)
        ->where('user_type', get_class($user))
        ->first();

    if ($existingLike) {
        return makeInst()->returnError("User has already liked this " . $likeableType);
    }
    // return makeInst() -> returnData("","",$owner);
    $like = new Like();
    $like->user()->associate($user);
    $like->likeable()->associate($likeable);
    $like->save();
    $channel_name = strtolower(class_basename($likeable->postable));
    $name = $user->username;
    if (!$name) {
        $name = $user->name;
    }

    $owner = $likeableType . 'able';
    broadcast(new Notifications($name . " reacted to your " . $likeableType, $channel_name, $likeable->$owner->id))->toOthers();
    fillNotification(class_basename($user), $user->id, class_basename($likeable->postable), $likeable->$owner->id, $name . " reacted to your " . $likeableType);
    return makeInst()->returnSuccess(ucfirst($likeableType) . " liked successfully");
}

function removeLike($request, $guard, $likeableType): \Illuminate\Http\JsonResponse
{

    $validator = Validator::make($request->all(), [
        $likeableType . '_id' => 'required|integer|exists:' . Str::plural($likeableType) . ',id'
    ]);

    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }

    $likeableId = $request->input($likeableType . '_id');
    $likeableClass = 'App\\Models\\' . ucfirst($likeableType);
    $likeable = $likeableClass::find($likeableId);

    $user = auth()->guard($guard)->user();

    if (!$user) {
        return makeInst()->returnError("invalid user");
    }

    $like = Like::where('likeable_id', $likeable->id)
        ->where('likeable_type', $likeableClass)
        ->where('user_id', $user->id)
        ->where('user_type', get_class($user))
        ->first();

    if (!$like) {
        return makeInst()->returnError("like not found");
    }

    $like->delete();

    return makeInst()->returnSuccess(ucfirst($likeableType) . " unliked successfully");
}

function fillNotification($senderType, $senderId, $reciverType, $reciverId, $content): void
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

function photo(Request $request, $diskName, $folderName, $id): bool|string
{
    $name = $id . $request->file("file")->getClientOriginalName();
    $path = $request->file("file")->storeAs($folderName, $name, $diskName);
    return $path;

}

function getFollowRecivedJobSeekers($user)
{
    $followers = Follow::where("followMaker_type", "App\Models\Job_seeker")
        ->where("followReciver_id", $user->id)->get("id");
    // $f = $user->followRecived;


    return $followers;
}

function applyService(Request $request, $guard): \Illuminate\Http\JsonResponse
{

    $service = Service::find($request->service_id);

    if (!$service) {
        return makeInst()->returnError("service not found");
    }

    if ($service->state == "processing") {
        return makeInst()->returnError("Service is not open for applications");
    }

    $user = Auth::guard($guard)->user();

    // تحقق مما إذا كان المستخدم قد تقدم بالفعل لهذه الخدمة
    $existingApplication = $user->makeApply()->where('service_id', $service->id)->first();
    if ($existingApplication) {
        return makeInst()->returnError("You have already applied for this service");
    }

    // إنشاء سجل جديد للتقديم على الخدمة
    $user->makeApply()->create([
        'service_id' => $service->id,
        'offer' => $request->offer,
        'isAccepted' => false,
    ]);

    broadcast(new Notifications("You have a new offer", "customer", $service->customer->id))->toOthers();
    fillNotification(class_basename($user), $user->id, "customer", $service->customer->id, "You have a new offer");

    return makeInst()->returnSuccess("You have successfully applied for the service");
}

function message(Request $request, $guard): \Illuminate\Http\JsonResponse
{

    $validator = Validator::make($request->all(), [
        "reciver_type" => "required",
        "reciver_id" => "required",
        "content" => "required",
    ]);
    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }
    $sender = Auth::guard($guard)->user();
    $reciverClass = 'App\\Models\\' . ucfirst($request->reciver_type);
    $reciver = $reciverClass::find($request->reciver_id);
    // return makeInst() -> returnData("", "", get_class($reciver));
    broadcast(new Notifications($request->content, strtolower(class_basename($reciver)), $reciver->id))->toOthers();
    $sender->sender()->create([
        "reciver_type" => get_class($reciver),
        "reciver_id" => $request->reciver_id,
        "content" => $request->content,
    ]);
    return makeInst()->returnSuccess("ok", 200);
}

function getMessages(Request $request, $guard): \Illuminate\Http\JsonResponse
{

    $validator = Validator::make($request->all(), [
        "reciver_type" => "required",
        "reciver_id" => "required",
    ]);

    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }

    $sender = Auth::guard($guard)->user();
    $reciverClass = 'App\\Models\\' . ucfirst($request->reciver_type);
    $reciver = $reciverClass::find($request->reciver_id);

    if (!$reciver) {
        return makeInst()->returnError("Receiver not found.");
    }

    $messages = Message::where(function ($query) use ($sender, $reciver) {
        $query->where('sender_id', $sender->id)
            ->where('reciver_id', $reciver->id);
    })
        ->orWhere(function ($query) use ($sender, $reciver) {
            $query->where('sender_id', $reciver->id)
                ->where('reciver_id', $sender->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    // Format the messages for the response
    $formattedMessages = $messages->map(function ($message) use ($sender) {
        return [
            "id" => $message->id,
            "reciver_type" => $message->reciver_type,
            "reciver_id" => $message->reciver_id,
            "sender_type" => $message->sender_type,
            "sender_id" => $message->sender_id,
            "content" => $message->content,
            "created_at" => $message->created_at,
            "updated_at" => $message->updated_at,
            "sent_by_user" => $message->sender_id === $sender->id // Check if the message was sent by the authenticated user
        ];
    });

    return makeInst()->returnData("", "messages", $formattedMessages);
}

function getNotifications(Request $request, $guard): \Illuminate\Http\JsonResponse
{

    $validator = Validator::make($request->all(), [
        "reciver_type" => "required",
        "reciver_id" => "required",
    ]);

    if ($validator->fails()) {
        return makeInst()->returnError($validator->errors()->first());
    }

    $reciverClass = 'App\\Models\\' . ucfirst($request->reciver_type);
    $reciver = $reciverClass::find($request->reciver_id);

    if (!$reciver) {
        return makeInst()->returnError("Receiver not found.");
    }

    // Fetch only the 'content' field of notifications
    $notifications = $reciver->notificationReciver()->orderBy('created_at', 'desc')->pluck('content');

    return makeInst()->returnData("", "notifications", $notifications);
}

function showProfile($type, $id): \Illuminate\Http\JsonResponse
{


    if (!($type && $id)) {
        return makeInst()->returnError();
    }

    $user = makeInst()->getUserByTypeAndId($type, $id);
    $posts = $user->posts;
    $user->posts = $posts;

    return makeInst()->returnData("", "profile", $user->makeHidden(["password", "verificationCode"]));

}

;
