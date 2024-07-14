<?php
namespace App\Traits;

use App\Models\Job_seeker;
use Auth;
use Validator;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Company;

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
            "file" => "required|file| max:2000",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $job_seeker = Auth::guard($guard)->user();
        $job_seeker->offers()->sync([
            $request->offer_id => [
                "CV" => $this->localStore($request, "CV", "job_seeker"),
                "additionalInfo" => $request->additionalInfo
            ]
        ]);
        return $this->returnSuccess("Successfully applied");
    }


    public function post($request, $guard, $who, $disk)
    {

        $validator = Validator::make($request->all(), [
            "title" => "required",
            "body" => "required",
            "file" => "required|file|max:50000"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        Post::create([
            "title" => $request->title,
            "body" => $request->body,
            "photo" => $this->localStore($request, "post", $disk),
            $who => getAuth($guard)->id,
        ]);
        return $this->returnSuccess("your post is published successfully");

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
        }
        return $this->returnSuccess("your comment created successfully");
    }

    public function update($request,$comment_id){
        $validator=Validator::make($request->all(),[
            "body" => "required"
        ]);
        if($validator->fails()){
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

}
