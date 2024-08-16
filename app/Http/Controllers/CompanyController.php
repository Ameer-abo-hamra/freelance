<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
use App\Models\Type;
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
        return $this->returnSuccess("your account deleted successfully");
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
        $categories = Category::get();
        return $this->returnData("", "categories", $categories);
    }

    public function getTypesSkills($categoryy_id)
    {
        $category=Category::findOrFail($categoryy_id);
        $types = $category->types()->get();
        return $this->returnData("types due category :","types",$types);
    }


    public function getSkillName($type_name)
    {
        $type=Type::where("type_name",$type_name)->firstOrFail();
        $skills = $type->skills()->get();
        return $this->returnData("", "skills", $skills);
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

        if($request->has("title")){
            $offer->title = $request->title;
        }
        if($request->has("body")){
            $offer->body = $request->body;
        }
        if($request->has("position")){
            $offer->position = $request->position;
        }
        if($request->has("type")){
            $offer->type = $request->type;
        }
        if($request->has("details")){
            $offer->details = $request->details;
        }
        $offer->save();

        return $this->returnSuccess("the offer updated successfully");
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
        return $this->post($request, "web-company", "post", "company");
    }

    public function updatePost_web(Request $request, $post_id)
    {
        return $this->updatePost($request, $post_id, "web-company", "post", "company");
    }
    public function updatePost_api(Request $request, $post_id)
    {
        return $this->updatePost($request, $post_id, "api-company", "post", "company");
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
            "followReceiverType" => "required",
            "followReceiverid" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        return putFollow($request->followMakerType, $request->followMakerid, $request->followReceiverType, $request->followReceiverid);


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
            'posts' => collect(),
            'offers' => collect(),
            'services' => collect()
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

        if ($filter == 'offers' || !$filter) {
            $results['offers'] = Offer::search($query)->get();
        }

        if ($filter == 'services' || !$filter) {
            $results['services'] = Service::search($query)->get();
        }

        return response()->json($results[$filter]);
    }



    public function show(Request $request)
    {
        return showProfile($request);
    }

    public function updateProfile_web(Request $request)
    {
        return $this->updateProfile($request, "web-company");
    }

    public function updateProfile_api(Request $request)
    {
        return $this->updateProfile($request, "api-company");

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
    public function messageWeb(Request $request)
    {
        return message($request, "web-company");
    }
    public function messageApi(Request $request)
    {
        return message($request, "api-company");
    }
    public function getMessages(Request $request)
    {
        return getMessages($request, "api-company");
    }

    public function getNotifications(Request $request)
    {
        return getNotifications($request, "api-company");
    }
}



