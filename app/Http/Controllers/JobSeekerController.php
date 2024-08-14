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
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserProfileResource;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceApply;

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
            "file" => "image|required|max:5048"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $id = Job_seeker::latest("id")->first();
        if ($id) {
            $job_seeker = Job_seeker::create([
                "username" => $request->username,
                "full_name" => $request->full_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birth_date" => $request->birth_date,
                "profile_photo" => photo($request, "job_seeker", "jobseeker", Job_seeker::latest("id")->first()->id + 1),
            ]);
        } else {
            $job_seeker = Job_seeker::create([
                "username" => $request->username,
                "full_name" => $request->full_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birth_date" => $request->birth_date,
                "profile_photo" => photo($request, "job_seeker", "jobseeker", 1),
            ]);
        }
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
        return $this->post($request, "api-job_seeker", "post", "job_seeker");
    }

    public function postWeb(Request $request)
    {
        return $this->post($request, "job_seeker", "post", "job_seeker");
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
        return $this->comment($request, "web-job_seeker", $post_id);
    }

    public function addComment_api(Request $request, $post_id)
    {
        return $this->comment($request, "api-job_seeker", $post_id);
    }

    public function updateComment(Request $request, $comment_id)
    {
        return $this->update($request, $comment_id);
    }


    public function deleteComment($comment_id)
    {
        Comment::find($comment_id)->delete();
        return $this->returnSuccess("comment deleted successfully");
    }

    public function addLikeToComment_api(Request $request)
    {
        return addLike($request, "api-job_seeker", "comment");
    }

    public function addLikeToPost_api(Request $request)
    {
        return addLike($request, "api-job_seeker", "post");
    }

    public function unlikePost_web(Request $request)
    {
        return removeLike($request, "web-job_seeker", "post");
    }

    public function unlikeComment_web(Request $request)
    {
        return removeLike($request, "web-job_seeker", "comment");
    }

    public function unlikePost_api(Request $request)
    {
        return removeLike($request, "api-job_seeker", "post");
    }

    public function unlikeComment_api(Request $request)
    {
        return removeLike($request, "api-job_seeker", "comment");
    }
       public function updatePost_web(Request $request, $post_id)
    {
        return $this->updatePost($request, $post_id, "web_job_seeker", "post", "job_seeker");
    }

    public function updatePost_api(Request $request, $post_id)
    {
        return $this->updatePost($request, $post_id, "api_job_seeker", "post", "job_seeker");
    }

    public function addLikeToPost_web(Request $request)
    {
        return addLike($request, "web-job_seeker", "post");
    }

    public function addLikeToComment_web(Request $request)
    {
        return addLike($request, "web-job_seeker", "comment");
    }

    public function deletePost($post_id)
    {
        $post = Post::find($post_id);
        $post->delete();
        return $this->returnSuccess("post deleted successfully");
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
    public function updateProfile(Request $request)
    {
        $user = getAuth("api-job_seeker");

        if (!$user) {
            return $this->returnError("user not found");
        }

        $request->validate([
            'username' => 'nullable|string|max:255',
            "full_name" => "nullable|string|max:255",
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "email" => "email|nullable",
            'password' => "nullable|max:255",
            "birth_date" => "date|nullable"
        ]);

        if ($request->has('username')) {
            $user->username = $request->input('username');
        }

        if ($request->has("full_name")) {
            $user->full_name = $request->input("full_name");
        }

        if ($request->has("email")) {
            $user->email = $request->input("email");
        }

        if ($request->has("password")) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        if ($request->has("birth_date")) {
            $user->birth_date = $request->input("birth_date");
        }
        $user->save();

        return $this->returnData('Profile updated successfully', "profile", $user);
    }

    public function deleteAccount($id)
    {
        $company = Job_seeker::findOrFail($id);
        $company->delete();
    }

    public function applyService(Request $request, $service_id)
    {

        $validator = Validator::make($request->all(), [
            'offer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }


        $service = Service::find($service_id);

        if (!$service) {
            return $this->returnError("service not found");
        }


        if ($service->state == "processing") {
            return $this->returnError("Service is not open for applications");
        }


        $job_seeker = Auth::guard("api-job_seeker")->user();

        ServiceApply::create([
            'applyable_type' => 'App\Models\Job_seeker',
            'applyable_id' => $job_seeker->id,
            'service_id' => $service->id,
            'offer' => $request->offer,
            'isAccepted' => false,
        ]);

        return $this->returnSuccess("You have successfully applied for the service");
    }

    public function applyServiceWeb(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offer' => 'required|string',
            "service_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }


        return applyService($request, "web-job_seeker");
    }
    public function applyServiceApi(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offer' => 'required|string',
            "service_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }


        return applyService($request, "api-job_seeker");
    }
    public function messageWeb(Request $request)
    {
        return message($request, "web-job_seeker");
    }
    public function messageApi(Request $request)
    {
        return message($request, "api-job_seeker");
    }

    public function commentsCount($post_id){
        return $this->CountOfComments($post_id);
    }

    public function likesCount($post_id){
        return $this->CountOfLikes($post_id);
    }

    public function commentslist($post_id){
        return $this->commentsOnPost($post_id);
    }


    public function showProfile(Request $request)
    {

        return showProfile($request);

    }

}
