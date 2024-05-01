<?php
namespace App\Traits;

use Auth;
use Validator;
use App\Models\Post;
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
        $job_seeker->offers()->attach($request->offer_id, [
            "CV" => $this->localStore($request, "CV", "job_seeker"),
            "additionalInfo" => $request->additionalInfo
        ]);
        return $this->returnSuccess("Successfully applied");
    }


    public function post($request ,$guard , $who,$disk ) {

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
}
