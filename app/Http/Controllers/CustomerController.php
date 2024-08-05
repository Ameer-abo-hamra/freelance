<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Contact_information;
use App\Traits\ResponseTrait;
use Auth;
use Hash;
use App\Mail\testmail;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Job_seeker;
use App\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Like;
use App\Http\Resources\UserProfileResource;

class CustomerController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "usrename" => "min:3||max:10",
            "full_name" => "min:3||max:20",
            "email" => "required | email |unique:customers",
            "password" => "required |min:8 | max:20"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        } else {
            $customer = Customer::create([
                "username" => $request->username,
                "full_name" => $request->full_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birth_date" => $request->birth_date,
                "verificationCode" => makeCode("customer", $request->email),
            ]);
            Auth::guard('customer')->login($customer);
            $credential = $request->only("username", "password");
            Auth::guard("api-customer")->attempt($credential);
            Customer::where("username", $request->username)->update([
                "verificationCode" => makeCode("customer", $request->email),
            ]);
            return $this->returnSuccess("your account created successfully");
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required||email",
            "password" => "required||min:8||max:20"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");
        try {
            if (auth()->guard("customer")->attempt($credential)) {
                $customer = auth::guard("customer")->user();


                return $this->returnData("U r logged-in successully", "customer data", $customer);
            }
        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function login_api(Request $request)
    {
        $validator = validator::make($request->all(), [
            "email" => "required | email ",
            "password" => "required | max:15 | min:6"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");

        // return $this->returnData("","",Auth::guard("api-customer")->user());

        if ($token = Auth::guard("api-customer")->attempt($credential)) {

            // $token = JWTAuth::fromUser($credential);
            return $this->returnData("U R logged-in successfully", "customer data", $token);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }


    public function logout_api()
    {

        try {
            auth("api-customer")->logout();
            return $this->returnSuccess("you are logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }

    }


    public function logout()
    {
        Auth::guard("customer")->logout();
        return $this->returnSuccess("you are logged out successfully");
    }

    public function verify(Request $request)
    {
        return verify($request, "customer");
    }

    public function apiVerify(Request $request)
    {
        return verify($request, "api-customer");

    }
    public function addService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "description" => "required",
            "skill_id" => "array||required",
            "price" => "required"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $service = Service::create([
            "description" => $request->description,
            "customer_id" => Auth::guard("customer")->user()->id,
            "price" => $request->price
            // "customer_id" => $request->customer_id
        ]);
        $skill_ids = $request->skill_id;
        if (!empty($skill_ids)) {
            foreach ($skill_ids as $s) {
                $service->skills()->attach($s);
            }
        } else {
            return $this->returnError("you have to enter some skills");
        }
        return $this->returnSuccess("your service is added successfully , wait to find anyone to solve it");
    }

    public function post_api(Request $request){
        return $this->post($request,"api-customer");
    }

    // public function updatePost(Request $request, $id, $guard, $who, $disk)
    // {
    //     $validator = Validator::make($request->all(), [
    //         "title" => "sometimes|required",
    //         "body" => "sometimes|required",
    //         "file" => "sometimes|file|max:50000"
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->returnError($validator->errors()->first());
    //     }

    //     $post = Post::find($id);

    //     if (!$post) {
    //         return $this->returnError("Post not found");
    //     }

    //     $user = getAuth($guard);

    //     if ($post->$who != $user->id) {
    //         return $this->returnError("You are not authorized to update this post");
    //     }

    //     if ($request->has('title')) {
    //         $post->title = $request->title;
    //     }

    //     if ($request->has('body')) {
    //         $post->body = $request->body;
    //     }

    //     if ($request->hasFile('file')) {
    //         if ($post->photo) {
    //             Storage::disk($disk)->delete($post->photo);
    //         }

    //         $post->photo = $this->localStore($request, "post", $disk);
    //     }

    //     $post->save();

    //     return $this->returnSuccess("Your post has been updated successfully");
    // }
    public function updatePost_api(Request $request,$post_id){
        return $this->updatePost($request,$post_id,"api-customer","customer","customer");
    }

    public function deletePost($post_id)
    {
        $post = Post::find($post_id);
        $post->delete();
        return $this->returnSuccess("post deleted successfully");
    }

    public function addService_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "description" => "required",
            "skill_id" => "array||required",
            "price" => "required"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $service = Service::create([
            "description" => $request->description,
            "customer_id" => Auth::guard("api-customer")->user()->id,
            "price" => $request->price
            // "customer_id" => $request->customer_id
        ]);
        $skill_ids = $request->skill_id;
        if (!empty($skill_ids)) {
            foreach ($skill_ids as $s) {
                $service->skills()->attach($s);
            }
        } else {
            return $this->returnError("you have to enter some skills");
        }
        return $this->returnSuccess("your service is added successfully , wait to find anyone to solve it");
    }

    public function updateService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "service_id" => "required",
            "description" => "string",
            "skill_id" => "array",
            "price" => "numeric"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $service = Service::find($request->service_id);

        if (!$service) {
            return $this->returnError("Service not found");
        }

        $customer = Auth::guard("api-customer")->user();
        if ($service->customer_id !== $customer->id) {
            return $this->returnError("You do not have permission to edit this service");
        }

        if ($request->description){
            $service->description = $request->description;
        }
        if ($request->price){
            $service->price = $request->price;
        }
        if($request->skill_id){
            $skill_ids = $request->skill_id;
            if (!empty($skill_ids)) {
                $service->skills()->sync($skill_ids);
            }
        }
        $service->save();

        return $this->returnSuccess("Service updated successfully");
    }

    public function deleteService($service_id){
        Service::find($service_id)->delete();
        return $this->returnSuccess("service deleted successfully");
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $jobSeekers = Job_seeker::search($query)->get();
        $companies = Company::search($query)->get();
        $customers = Customer::search($query)->get();
        $posts = Post::search($query)->get();

        if ($jobSeekers || $companies || $customers || $posts) {
            $results = [
                'job_seekers' => $jobSeekers,
                'companies' => $companies,
                'customers' => $customers,
                'posts' => $posts
            ];
        }

        return response()->json($results);
    }

    public function searchWithFilter(Request $request)
    {
        $query = $request->input('query');
        $filter = $request->input('filter');

        $results = [
            'job_seekers' => collect(),
            'companies' => collect(),
            'customers' => collect(),
            'posts' => collect()
        ];


        if ($filter == 'job_seekers' || !$filter) {
            $results['job_seekers'] = Job_seeker::search($query)->get();
        }

        if ($filter == 'companies' || !$filter) {
            $results['companies'] = Company::search($query)->get();
        }

        if ($filter == 'customers' || !$filter) {
            $results['customers'] = Customer::search($query)->get();
        }

        if ($filter == 'posts' || !$filter) {
            $results['posts'] = Post::search($query)->get();
        }

        return response()->json($results[$filter]);
    }
    private function getUserByTypeAndId($type, $id)
    {
        switch ($type) {
            case 'Job_seeker':
                return Job_seeker::find($id);
            case 'Company':
                return Company::find($id);
            case 'Customer':
                return Customer::find($id);
            default:
                return null;
        }
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

    public function showCustomers()
    {
        $customers = Customer::get();
        return $customers;
    }


    public function addComment(Request $request,$post_id){
        return $this->comment($request,"api-customer",$post_id);
    }

    public function updateComment(Request $request,$comment_id){
        return $this->update($request,$comment_id);
    }

    public function deleteComment($comment_id)
    {
        Comment::find($comment_id)->delete();
        return $this->returnSuccess("comment deleted successfully");
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

        if ($request->user_type == "App\\Models\\Customer") {
            $user = auth()->guard('api-customer')->user();
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

        if ($request->user_type == "App\\Models\\Customer") {
            $user = auth()->guard('api-customer')->user();
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

        if ($request->user_type == "App\\Models\\Customer") {
            $user = auth()->guard('api-customer')->user();
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

        if ($request->user_type == "App\\Models\\Customer") {
            $user = auth()->guard('api-customer')->user();
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

    public function show($type, $id)
    {
        $user = $this->getUserByTypeAndId($type, $id);

        if (!$user) {
            return $this->returnError("User not found");
        }

        $posts = Post::where('postable_id', $id)
            ->where('postable_type', "App\\Models\\$type")
            ->with(['comments.likes', 'likes'])
            ->get();
        $user->posts = $posts;
        if ($posts) {
            $user->load(['posts.comments.likes', 'posts.likes']);
        }
        return new UserProfileResource($user);
    }

    // public function updateProfile_api(Request $request)
    // {
    //     return $this->updateProfile($request,"api-customer");
    // }


    public function updateProfile(Request $request)
    {
        $user = getAuth("api-customer");

        if (!$user) {
            return $this->returnError("user not found");
        }

        $request->validate([
            'username' => 'nullable|string|max:255',
            "full_name" => "nullable|string|max:255",
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "email" => "email|nullable",
            'password' =>"nullable|max:255",
            "birth_date" =>"date|nullable"
        ]);

        if ($request->has('username')) {
            $user->username = $request->input('username');
        }

        if($request->has("full_name")){
            $user->full_name = $request->input("full_name");
        }

        if($request->has("email")){
            $user->email = $request->input("email");
        }

        if($request->has("password")){
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        if($request->has("birth_date")){
            $user->birth_date=$request->input("birth_date");
        }
        $user->save();

        return $this->returnData('Profile updated successfully', "profile", $user);
    }
    public function deleteAccount($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return $this->returnSuccess("account deleted successfully");
    }


}


