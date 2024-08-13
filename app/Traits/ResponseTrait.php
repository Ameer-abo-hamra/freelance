<?php
namespace App\Traits;

use App\Events\Notifications;
use App\Models\Job_seeker;
use App\Models\Offer;
use Auth;
use Validator;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

trait ResponseTrait
{
    public static function returnError($msgErorr = "", $errorNumber = 400)
    {

        return response()->json([
            "status" => false,
            "message" => $msgErorr,
            "statusNumber" => $errorNumber
        ]);

    }
    public static function returnSuccess($msgSuccess = "", $succesNumber = 200)
    {

        return response()->json([
            "status" => true,
            "message" => $msgSuccess,
            "statusNumber" => $succesNumber
        ]);

    }

    public static function returnData($msgData = "", $key, $data = [], $responseNumber = 200)
    {
        return response()->json([
            "status" => true,
            "message" => $msgData,
            "statusNumber" => $responseNumber,
            "$key" => $data,
        ]);
    }
    public function localStore($request, $directory, $disk)
    {
        $file_extention = $request->file("file")->getClientOriginalExtension();
        $file_name = time() . "." . $file_extention;
        return $request->file("file")->storeAs($directory, $file_name, $disk);

    }

    public function apply($request, $guard)
    {
        $validator = Validator::make($request->all(), [
            "file" => "required|file|max:5000",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $job_seeker = Auth::guard($guard)->user();

        $existingApplication = $job_seeker->offers()->where('offer_id', $request->offer_id)->exists();
        if ($existingApplication) {
            return $this->returnError("You have already applied for this job opportunity.");
        }

        $job_seeker->offers()->sync([
            $request->offer_id => [
                "CV" => photo($request, "job_seeker", "CVs", $job_seeker->id),
                "additionalInfo" => $request->additionalInfo
            ]
        ]);

        $company = Offer::find($request->offer_id)->company;
        broadcast(new Notifications($job_seeker->username . ' applied to your job opportunity', "company", $company->id));
        fillNotification("jobseeker", $job_seeker->id, "company", $company->id, $job_seeker->username . ' applied to your job opportunity');

        return $this->returnSuccess("Successfully applied");
    }



    public function post($request, $guard, $folderName, $diskName)
    {

        $validator = Validator::make($request->all(), [
            "title" => "required",
            "body" => "required",
            "photo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $user = getAuth($guard);
        $user->posts()->create([
            "photo" => photo($request, $diskName, $folderName, $user->id),
            "title" => $request->title,
            "body" => $request->body
        ]);

        return $this->returnSuccess("your post is published successfully");
    }

    public function updatePost($request, $id, $guard, $who, $disk)
    {
        $validator = Validator::make($request->all(), [
            "title" => "sometimes|required",
            "body" => "sometimes|required",
            "file" => "sometimes|file|max:50000"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($id);

        if (!$post) {
            return $this->returnError("Post not found");
        }

        $user = getAuth($guard);

        if ($post->$who != $user->id) {
            return $this->returnError("You are not authorized to update this post");
        }

        if ($request->has('title')) {
            $post->title = $request->title;
        }

        if ($request->has('body')) {
            $post->body = $request->body;
        }

        if ($request->hasFile('file')) {
            if ($post->photo) {
                Storage::disk($disk)->delete($post->photo);
            }

            $post->photo = $this->localStore($request, "post", $disk);
        }

        $post->save();

        return $this->returnSuccess("Your post has been updated successfully");
    }


    public function comment($request, $guard, $post_id)
    {
        $validator = Validator::make($request->all(), [
            "body" => "required|string"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $post = Post::find($post_id);
        if (!$post) {
            return $this->returnError("post isn't found");
        }
        $comment = new Comment();
        $comment->body = $request->body;
        $comment->post_id = $post_id;
        if ($guard == "web-job_seeker") {
            $job_seeker_id = getAuth("web-job_seeker")->id;
            $comment->commentable_type = Job_seeker::class;
            $comment->commentable_id = $job_seeker_id;
            $comment->save();
        } elseif ($guard == "web-company") {
            $company_id = getAuth("web-company")->id;
            $comment->commentable_type = Company::class;
            $comment->commentable_id = $company_id;
            $comment->save();
        } elseif ($guard == "api-company") {
            $company_id = getAuth("api-company")->id;
            $comment->commentable_type = Company::class;
            $comment->commentable_id = $company_id;
            $comment->save();
        } elseif ($guard == "api-job_seeker") {
            $job_seeker_id = getAuth("api-job_seeker")->id;
            $comment->commentable_type = Job_seeker::class;
            $comment->commentable_id = $job_seeker_id;
            $comment->save();
        }
        return $this->returnSuccess("your comment created successfully");
    }

    public function update($request, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            "body" => "required"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $comment = Comment::find($comment_id);
        if (!$comment) {
            return $this->returnError("Comment not found");
        }
        $comment->body = $request->input("body");
        $comment->save();
        return $this->returnSuccess("comment updated successfully");
    }


    public function updateProfile($request, $guard)
    {
        $user = getAuth($guard);

        if (!$user) {
            return $this->returnError("user not found");
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }
        $user->save();

        return $this->returnData('Profile updated successfully', "profile", $user);
    }

    public static function getUserByTypeAndId($type, $id)
    {
        // تحويل القيمة إلى أحرف صغيرة
        $type = strtolower($type);

        switch ($type) {
            case 'job_seeker':
                return Job_seeker::find($id);
            case 'company':
                return Company::find($id);
            case 'customer':
                return Customer::find($id);
            default:
                return null;
        }
    }

}
