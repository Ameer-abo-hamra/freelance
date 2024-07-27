<?php

namespace App\Http\Controllers;

use App\Models\Job_seeker;
use App\Models\Post;
use App\Traits\ResponseTrait;
use Validator;
use Illuminate\Http\Request;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Hash;
use App\Models\Comment;
use App\Models\Like;




class JobSeekerController extends Controller
{

    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "username" => "unique:job_seekers||required||min:5||max:15",
            "full_name" => "required | min:6 |max:20",
            "password" => "required",
            "birth_date" => "required | date",
            "email" => "required |unique:job_seekers",

        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $job_seeker = Job_seeker::create([
            "username" => $request->username,
            "full_name" => $request->full_name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "birth_date" => $request->birth_date,
        ]);
        $credential = $request->only("username", "password");
        Auth::guard("api-job_seeker")->attempt($credential);
        Auth::guard("web-job_seeker")->login($job_seeker);
        Job_seeker::where("username", $request->username)->update([
            "verificationCode" => makeCode("job_seeker", $request->email),
        ]);
        // Auth::guard("api-job_seeker")->login($job_seeker);
        return $this->returnSuccess("your account created successfully");
        // return $this->returnData("", "", Auth::guard('api-job_seeker')->user()->username);
    }
    public function verifyWeb(Request $request)
    {

        return verify($request, "web-job_seeker");
    }

    public function apiVerify(Request $request)
    {
        return verify($request, "api-job_seeker");

    }
    public function resend()
    {
        makeCode("company", Auth::guard("web-company")->user()->email);
        return $this->returnSuccess("check your email please :)");
    }
    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "email" => "required| email",
            "password" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");

        if (Auth::guard("web-job_seeker")->attempt($credential)) {
            $job_seeker = Auth::guard("web-job_seeker")->user();
            return $this->returnData("U R logged-in successfully", "job_seeker data", $job_seeker);
        }
        return $this->returnError("your data is invalid .. please enter it again");
    }
    public function logout()
    {
        Auth::guard("web-job_seeker")->logout();
        return $this->returnSuccess("you are logged-out successfully");
    }
    public function logout_api(Request $request)
    {

        try {
            $user = auth("api-job_seeker")->user();
            auth("api-job_seeker")->logout();
            return $this->returnSuccess("you are logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }

    }
    public function login_api(Request $request)
    {
        $validator = validator::make($request->all(), [
            "email" => "required|email",

            "password" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");
        $token = Auth::guard("api-job_seeker")->attempt($credential);
        if ($token) {
            $job_seeker = Auth::guard("api-job_seeker")->user();
            $job_seeker->api = $token;
            return $this->returnData("U R logged-in successfully", "job_seeker data", $job_seeker);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function applyApi(Request $request)
    {
        return $this->apply($request, "api-job_seeker");
    }

    public function applyWeb(Request $request)
    {
        return $this->apply($request, "web-job_seeker");
    }
    public function postApi(Request $request)
    {
        return $this->post($request, "api-job_seeker", "job_seeker_id", "job_seeker");
    }

    public function postWeb(Request $request)
    {
        return $this->post($request, "job_seeker", "job_seeker_id", "job_seeker");
    }

    public function getCategory()
    {
        return getCategoryApi("api-job_seeker");
    }

    public function apply_()
    {
        $job_seeker = Job_seeker::find(1);
        $job_seeker->makeApply()->create();
    }
    public function browse(Request $request)
    {

        $validator = validator::make($request->all(), [
            "type" => "required",
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        return browse($request->type, $request->id);
    }

    public function putFollow(Request $request)
    {

        $validator = validator::make($request->all(), [
            "followMakerType" => "required",
            "followMakerid" => "required",
            "followReciverType" => "required",
            "followReciverid" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        return putFollow($request->followMakerType, $request->followMakerid, $request->followReciverType, $request->followReciverid);
    }

    public function showJob_seekers()
    {
        $job_seekers = Job_seeker::get();
        return $job_seekers;
    }

    public function addComment_web(Request $request, $post_id)
    {
        return $this->comment($request, "web-job_seeker",$post_id);
    }

    public function addComment(Request $request,$post_id){
        return $this->comment($request,"api-job_seeker",$post_id);
    }

    public function updateComment(Request $request,$comment_id){
        return $this->update($request,$comment_id);
    }

    public function deleteComment($comment_id)
    {
        Comment::find($comment_id)->delete();
        return $this->returnSuccess("comment deleted successfully");
    }

    public function addLikeToComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $comment = Comment::find($request->comment_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('web-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("invalid user");
        }

        $existingLike = Like::where('likeable_id', $comment->id)
                            ->where('likeable_type', 'App\\Models\\Comment')
                            ->where('user_id', $user->id)
                            ->where('user_type', get_class($user))
                            ->first();

        if ($existingLike) {
            return $this->returnError("User has already liked this comment");
        }

        $like = new Like();
        $like->user()->associate($user);
        $like->likeable()->associate($comment);
        $like->save();

        return $this->returnSuccess("Comment liked successfully");
    }
    public function addLikeToComment_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $comment = Comment::find($request->comment_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('api-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("invalid user");
        }

        $existingLike = Like::where('likeable_id', $comment->id)
                            ->where('likeable_type', 'App\\Models\\Comment')
                            ->where('user_id', $user->id)
                            ->where('user_type', get_class($user))
                            ->first();

        if ($existingLike) {
            return $this->returnError("User has already liked this comment");
        }

        $like = new Like();
        $like->user()->associate($user);
        $like->likeable()->associate($comment);
        $like->save();

        return $this->returnSuccess("Comment liked successfully");
    }

    public function unlikeComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $comment = Comment::find($request->comment_id);

        $user = null;

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('web-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("Invalid user");
        }

        $existingLike = Like::where('likeable_id', $comment->id)
                            ->where('likeable_type', 'App\\Models\\Comment')
                            ->where('user_id', $user->id)
                            ->where('user_type', get_class($user))
                            ->first();

        if (!$existingLike) {
            return $this->returnError("Like not found");
        }

        $existingLike->delete();

        return $this->returnSuccess("Comment unliked successfully");
    }
    public function unlikeComment_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $comment = Comment::find($request->comment_id);

        $user = null;

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('api-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("Invalid user");
        }

        $existingLike = Like::where('likeable_id', $comment->id)
                            ->where('likeable_type', 'App\\Models\\Comment')
                            ->where('user_id', $user->id)
                            ->where('user_type', get_class($user))
                            ->first();

        if (!$existingLike) {
            return $this->returnError("Like not found");
        }

        $existingLike->delete();

        return $this->returnSuccess("Comment unliked successfully");
    }

    public function addLikeToPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('web-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("user is invalid");
        }


        $existingLike = Like::where('likeable_id', $post->id)
            ->where('likeable_type', 'App\\Models\\Post')
            ->where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->first();

        if ($existingLike) {
            return $this->returnError("User has already liked this post");
        }
        $like = new Like();
        $like->user()->associate($user);
        $like->likeable()->associate($post);
        $like->save();

        return $this->returnSuccess("post liked successfully");
    }
    public function addLikeToPost_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('api-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("user is invalid");
        }


        $existingLike = Like::where('likeable_id', $post->id)
            ->where('likeable_type', 'App\\Models\\Post')
            ->where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->first();

        if ($existingLike) {
            return $this->returnError("User has already liked this post");
        }
        $like = new Like();
        $like->user()->associate($user);
        $like->likeable()->associate($post);
        $like->save();

        return $this->returnSuccess("post liked successfully");
    }

    public function unlikePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('web-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("user is invalid");
        }

        $like = Like::where('likeable_id', $post->id)
                    ->where('likeable_type', 'App\\Models\\Post')
                    ->where('user_id', $user->id)
                    ->where('user_type', get_class($user))
                    ->first();

        if (!$like) {
            return $this->returnError("like not found");
        }

        $like->delete();

        return $this->returnSuccess("Post unliked successfully");

    }
    public function unlikePost_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Job_seeker") {
            $user = auth()->guard('api-job_seeker')->user();
        }

        if (!$user) {
            return $this->returnError("user is invalid");
        }

        $like = Like::where('likeable_id', $post->id)
                    ->where('likeable_type', 'App\\Models\\Post')
                    ->where('user_id', $user->id)
                    ->where('user_type', get_class($user))
                    ->first();

        if (!$like) {
            return $this->returnError("like not found");
        }

        $like->delete();

        return $this->returnSuccess("Post unliked successfully");

    }



}
