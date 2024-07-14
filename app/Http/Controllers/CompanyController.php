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
use App\Models\Like;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
            "password" => "required",
            "employee_number" => "required |integer | min:10 | max:500000",
            "establishment_date" => "required | date",
            "email" => "required | unique:companies| email",
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
            // "verificationCode" => makeCode("company", $request->email),
        ]);
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

    public function getOffers($company_id)
    {

        $company = getAuth("web-company");

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

    public function ChangeOfferState(Request $request)
    {

        $validator = validator::make($request->all(), [
            "state" => "required",
            "offer_id" => "required ",
            "job_seeker_id" => "required",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
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
        return $this->returnError("this offer does not exist");
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

    public function addComment(Request $request, $post_id)
    {
        return $this->comment($request, "web-company", $post_id);
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
        } elseif ($request->user_type == "App\\Models\\Company") {
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

        // if ($request->user_type == "App\\Models\\Job_seeker") {
        //     $user = auth()->guard('web-job_seeker')->user();
        // } else
        if ($request->user_type == "App\\Models\\Company") {
            $user = auth()->guard('web-company')->user();
        }

        if (!$user) {
            return response()->json(['error' => 'Invalid user'], 400);
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


}
