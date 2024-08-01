<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job_seeker;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Report;
use App\Traits\ResponseTrait;
use PHPUnit\Framework\Constraint\IsEmpty;
use Validator;
use Auth;
use Hash;
use App\Events\RespondApplicants;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Skill;
use App\Models\Post;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Resources\UserProfileResource;
use App\Traits\StorePhotoTrait;

class CompanyController extends Controller
{
    use ResponseTrait, StorePhotoTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
            "password" => "required",
            "employee_number" => "required |integer | min:10 | max:500000",
            "establishment_date" => "required | date",
            "email" => "required | unique:companies| email",
            "profile_photo" => "image||mimes:jpeg,png,jpg,gif|max:2048"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $company = Company::create([
            "name" => $request->name,
            "password" => Hash::make($request->password),
            "establishment_date" => $request->establishment_date,
            "employee_number" => $request->employee_number,
            "email" => $request->email,
            "profile_photo" => $request->profile_photo
            // "verificationCode" => makeCode("company", $request->email),
        ]);
        $company->profile_photo = isset($request["profile_photo"])
            ? $this->store($request["profile_photo"], "profile_photos")
            : null;

        Auth::guard("web-company")->login($company);
        $credential = $request->only("name", "password");
        Auth::guard("api-company")->attempt($credential);
        Company::where("name", $request->name)->update([
            "verificationCode" => makeCode("company", $request->email),
        ]);
        return $this->returnSuccess("your account created successfully");
    }

    public function verify(Request $request)
    {
        return verify($request, "web-company");
    }
    public function apiVerify(Request $request)
    {
        return verify($request, "api-company");

    }
    public function resend()
    {
        $user = Auth::guard("web-company")->user();
        $user->update([
            "verificationCode" => makeCode("company", $user->email)
        ]);
        return $this->returnSuccess("check your email please :)");
    }

    public function login_api(Request $request)
    {
        $validator = validator::make($request->all(), [
            "email" => "required| email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $credential = $request->only("email", "password");
        $token = Auth::guard("api-company")->attempt($credential);
        if ($token) {
            $company = Auth::guard("api-company")->user();
            $company->api = $token;
            return $this->returnData("U R logged-in successfully", "company data", $company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }


    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required| max:15",
            "password" => "required | min:8| max:20",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $credential = $request->only("name", "password");


        if (Auth::guard("web-company")->attempt($credential)) {
            $company = Auth::guard("web-company")->user();
            return $this->returnData("U R logged-in successfully", "company data", $company);
        }
        return $this->returnError("your data is invalid .. enter it again");
    }

    public function deleteAccount($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
    }


    public function logout_api(Request $request)
    {

        try {
            auth("api-company")->logout();
            return $this->returnSuccess("you are logged-out successfully");
        } catch (JWTException $e) {
            return $this->returnError("there were smth wrong");
        }

    }


    public function getCategory()
    {
        $categories = Skill::distinct()->get("category");
        $arr = [];
        foreach ($categories as $cat) {
            array_push($arr, $cat["category"]);
        }
        return $this->returnData("", "categories", $arr);
    }

    public function getTypesSkills($category)
    {
        $types = Skill::where("category", $category)->distinct()->get("type");
        $arr = [];
        foreach ($types as $cat) {
            array_push($arr, $cat["type"]);
        }
        return $this->returnData("", "types", $arr);
    }


    public function getSkillName($type)
    {
        $skills = Skill::where("type", $type)->get("skill_name");
        $arr = [];
        foreach ($skills as $cat) {
            array_push($arr, $cat["skill_name"]);
        }
        return $this->returnData("", "skill_names", $arr);

    }

    public function addOfferWeb(Request $request)
    {
        return addOffer($request, "web-company");
    }
    public function addOfferApi(Request $request)
    {
        return addOffer($request, "api-company");
    }

    public function offerUpdate(Request $request)
    {

        $offer = Offer::find($request->offer_id);
        if ($offer) {
            $offer->update([
                "title" => $request->title,
                "body" => $request->body,
                "position" => $request->position,
                "type" => $request->type,
                "details" => $request->details,
            ]);

            $offer->skills()->sync($request->skill_ids);
            return $this->returnSuccess("your data is updated");
        }

        return $this->returnError("the offer id is not correct ");
    }
    public function log_out()
    {
        Auth::guard("web-company")->logout();
        return $this->returnSuccess("U R logged-out successfully");
    }

    public function postApi(Request $request)
    {

        return $this->post($request, "api-company", "company_id", "company");
    }

    public function postWeb(Request $request)
    {
        return $this->post($request, "company", "company_id", "company");
    }

    public function updatePost(Request $request, $id, $guard, $who, $disk)
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

    public function deletePost($post_id)
    {
        $post = Post::find($post_id);
        $post->delete();
        return $this->returnSuccess("post deleted successfully");
    }

    public function getOffers($company_id)
    {

        $company = Company::find($company_id);

        if ($company) {
            return $this->returnData("", "offers", $company->offers);
        }
        return $this->returnError("check company id :)");

    }

    public function getJobApplicants($offer_id)
    {

        $offer = Offer::find($offer_id);

        if ($offer) {
            return $this->returnData("", "applicants", $offer->jobSeekers);
        }
        return $this->returnError("check offer id :)");

    }

    public function ChangeOfferStateWeb(Request $request)
    {

        $validator = validator::make($request->all(), [
            "state" => "required",
            "offer_id" => "required ",
            "job_seeker_id" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        return ChangeOfferState($request, "web-company");
    }
    public function ChangeOfferStateApi(Request $request)
    {


        $validator = validator::make($request->all(), [
            "state" => "required",
            "offer_id" => "required ",
            "job_seeker_id" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        ;
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
                        broadcast(new RespondApplicants(getAuth("web-company")->name, $request->state, $content));

                    } else {
                        $content = "Your employment application has been rejected by " . getAuth("web-company")->name;
                        broadcast(new RespondApplicants(getAuth("web-company")->name, $request->state, $content));
                    }

                    getAuth("web-company")->notificationSent()->create([
                        "notfiReciver_type" => "app\Models\Job_seeker",
                        "notfiReciver_id" => $request->job_seeker_id,
                        "content" => $content
                    ]);
                    return $this->returnSuccess("this order is changed ");
                }
            }
            return $this->returnError("this jobSeeker did not apply for this offer");
        }
        return ChangeOfferState($request, "api-company");

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

    public function addComment_web(Request $request, $post_id)
    {
        return $this->comment($request, "web-company", $post_id);
    }

    public function addComment_api(Request $request, $post_id)
    {
        return $this->comment($request, "api-company", $post_id);
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

    public function showCompanies()
    {
        $companies = Company::get();
        return $companies;
    }


    public function addLikeToPost_web(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('web-company')->user();
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

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('api-company')->user();
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

    public function unlikePost_web(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($request->post_id);

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('web-company')->user();
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

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('api-company')->user();
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

    public function addLikeToComment_web(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $comment = Comment::find($request->comment_id);

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('web-company')->user();
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

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('api-company')->user();
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

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('api-company')->user();
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

    public function unlikeComment_web(Request $request)
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

        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('web-company')->user();
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

    public function updateProfile_web(Request $request)
    {
        return $this->updateProfile($request, "web-company");
    }

}
