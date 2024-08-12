<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job_seeker;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Report;
use App\Models\Service;
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
use App\Models\ServiceApply;
use App\Events\Notifications;

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
            "file" => "required|image|max:5048"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $id = Company::latest("id")->first();
        if ($id) {
            # code...

            $company = Company::create([
                "name" => $request->name,
                "password" => Hash::make($request->password),
                "establishment_date" => $request->establishment_date,
                "employee_number" => $request->employee_number,
                "email" => $request->email,
                "profile_photo" => photo($request, "company", "profile", Company::latest("id")->first()->id + 1),
                "verificationCode" => makeCode("company", $request->email),
            ]);
        } else {
            $company = Company::create([
                "name" => $request->name,
                "password" => Hash::make($request->password),
                "establishment_date" => $request->establishment_date,
                "employee_number" => $request->employee_number,
                "email" => $request->email,
                "profile_photo" => photo($request, "company", "profile", 1),
                "verificationCode" => makeCode("company", $request->email),
            ]);
        }


        Auth::guard("web-company")->login($company);
        $credential = $request->only("name", "password");
        Auth::guard("api-company")->attempt($credential);

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

        return $this->post($request, "api-company", "post", "company");
    }

    public function postWeb(Request $request)
    {
        return $this->post($request, "company", "post", "company");
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
        return addLike($request, "web-company", "post");
    }

    public function addLikeToComment_web(Request $request)
    {
        return addLike($request, "web-company", "comment");
    }

    public function addLikeToComment_api(Request $request)
    {
        return addLike($request, "api-company", "comment");
    }

    public function addLikeToPost_api(Request $request)
    {
        return addLike($request, "api-company", "post");
    }

    public function unlikePost_web(Request $request)
    {
        return removeLike($request, "web-company", "post");
    }

    public function unlikePost_api(Request $request)
    {
        return removeLike($request, "api-company", "post");
    }

    public function unlikeComment_web(Request $request)
    {
        return removeLike($request, "web-company", "comment");
    }

    public function unlikeComment_api(Request $request)
    {
        return removeLike($request, "api-company", "comment");
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

    public function updateProfile_api(Request $request)
    {
        return $this->updateProfile($request, "api-company");

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


        return applyService($request, "web-company");
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


        return applyService($request, "api-company");
    }
}



