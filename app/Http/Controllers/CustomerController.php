<?php

namespace App\Http\Controllers;

use App\Models\Offer;
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
use App\Models\Wallet;
use App\Models\Job_seeker;
use App\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Like;
use App\Http\Resources\UserProfileResource;
use App\Models\ServiceApply;

class CustomerController extends Controller
{
    use ResponseTrait;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "unique:customers|min:3||max:10",
            "full_name" => "min:3||max:20",
            "email" => "required | email |unique:customers",
            "password" => "required |min:8 | max:20",
            "file" => "required|image|max:5048"

        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $id = Customer::latest("id")->first();
        if ($id) {
            $customer = Customer::create([
                "username" => $request->username,
                "full_name" => $request->full_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birth_date" => $request->birth_date,
                "profile_photo" => photo($request, "customer", "profile", Customer::latest("id")->first()->id + 1),
                "verificationCode" => makeCode("customer", $request->email),
            ]);
        } else {
            $customer = Customer::create([
                "username" => $request->username,
                "full_name" => $request->full_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birth_date" => $request->birth_date,
                "profile_photo" => photo($request, "customer", "profile", 1),
                "verificationCode" => makeCode("customer", $request->email),
            ]);
        }
        Auth::guard('customer')->login($customer);
        $credential = $request->only("username", "password");
        Auth::guard("api-customer")->attempt($credential);
        return $this->returnSuccess("your account created successfully");

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
            "price" => "required",
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

    public function getApplicants($serviceId)
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return $this->returnError("Service not found.");
        }

        $applicants = $service->appliers;


        // ->map(function ($apply) {
        //     $applicant = $apply->applyable;

        //     switch (class_basename($applicant)) {
        //         case 'Company':
        //             $name = $applicant->name;
        //             break;
        //         case 'JobSeeker':
        //             $name = $applicant->username;
        //             break;
        //         default:
        //             $name = 'Unknown';
        //             break;
        //     }

        //     return [
        //         'name' => $name,
        //         'offer' => $apply->offer
        //     ];
        // });

        return $this->returnData("", "applicants", $applicants->makeHidden("created_at"));
    }


    public function post_api(Request $request)
    {

        return $this->post($request, "api-customer", "post", "customer");
    }

    public function postWeb(Request $request)
    {
        return $this->post($request, "web-company", "post", "company");
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
    public function updatePost_api(Request $request, $post_id)
    {
        return $this->updatePost($request, $post_id, "api-customer", "post", "customer");
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

        if ($request->description) {
            $service->description = $request->description;
        }
        if ($request->price) {
            $service->price = $request->price;
        }
        if ($request->skill_id) {
            $skill_ids = $request->skill_id;
            if (!empty($skill_ids)) {
                $service->skills()->sync($skill_ids);
            }
        }
        $service->save();

        return $this->returnSuccess("Service updated successfully");
    }

    public function deleteService($service_id)
    {
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


    public function addComment(Request $request, $post_id)
    {
        return $this->comment($request, "api-customer", $post_id);
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

    public function addLikeToPost_web(Request $request)
    {
        return addLike($request, "customer", "post");
    }

    public function addLikeToComment_web(Request $request)
    {
        return addLike($request, "customer", "comment");
    }

    public function addLikeToPost_api(Request $request)
    {
        return addLike($request, "api-customer", "post");
    }

    public function addLikeToComment_api(Request $request)
    {
        return addLike($request, "api-customer", "comment");
    }

    public function unlikePost_web(Request $request)
    {
        return removeLike($request, "customer", "post");
    }

    public function unlikeComment_web(Request $request)
    {
        return removeLike($request, "customer", "comment");
    }

    public function unlikePost_api(Request $request)
    {
        return removeLike($request, "api-customer", "post");
    }

    public function unlikeComment_api(Request $request)
    {
        return removeLike($request, "api-customer", "comment");
    }

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
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return $this->returnSuccess("account deleted successfully");
    }

    public function showServiceAppliers($serviceId)
    {
        $service = Service::with('appliers')->find($serviceId);

        if (!$service) {
            return $this->returnError("Service not found.");
        }

        return $this->returnSuccess("Service appliers fetched successfully.", $service->appliers);
    }

    public function acceptServiceApplier(Request $request, $serviceId)
    {
        $customer = Auth::guard('api-customer')->user();
        $service = Service::where('customer_id', $customer->id)->find($serviceId);

        if (!$service) {
            return $this->returnError("Service not found or you do not have permission to accept this service.");
        }


        $applier = ServiceApply::where('service_id', $serviceId)
            ->where('applyable_id', $request->applierId)
            ->where('applyable_type', "App\\Models\\" . ucfirst($request->applierType))
            ->where('isAccepted', false)
            ->first();

        if (!$applier) {
            return $this->returnError("Applier not found");
        }
        $customerWallet = Wallet::where('customer_id', $customer->id)->first();

        if ($customerWallet->balance < $service->price) {
            return $this->returnError("you can't accept any applier because you haven't money");
        } elseif ($customerWallet->balance >= $service->price) {
            $applier->isAccepted = true;
            $applier->save();

            $service->state = 'processing';
            $service->save();

            if (!$customerWallet) {
                \Log::error("Customer wallet not found for customer ID: " . $customer->id);
                return $this->returnError("Customer wallet not found.");
            }

            \Log::info("Customer wallet found: " . json_encode($customerWallet));

            $customerWallet->reserved = $customerWallet->reserved + $service->price;
            $customerWallet->save();
            return $this->returnSuccess("Applier accepted and service status updated successfully.");
        }
    }
    public function markServiceAsDone(Request $request, $serviceId)
    {
        $customer = Auth::guard('api-customer')->user();
        $service = Service::where('customer_id', $customer->id)->find($serviceId);

        if (!$service) {
            return $this->returnError("Service not found or you do not have permission to mark this service as done.");
        }

        $acceptedApplier = ServiceApply::where('service_id', $serviceId)
            ->where('isAccepted', true)
            ->first();

        if (!$acceptedApplier) {
            return $this->returnError("No accepted applier found for this service.");
        }
        $service->state = 'done';
        $service->save();
        $customerWallet = Wallet::where('customer_id', $customer->id)->first();

        if (!$customerWallet) {
            return $this->returnError("Customer wallet not found.");
        }

        $customerWallet->reserved = $customerWallet->reserved - $service->price;
        $customerWallet->balance = $customerWallet->balance - $service->price;
        $customerWallet->save();

        $applier = $acceptedApplier->applyable;

        if (!$applier) {
            return $this->returnError("Applier not found.");
        }

        \Log::info("Applier found: " . json_encode($applier));

        if ($applier instanceof Job_seeker) {
            $applierWallet = Wallet::where("job_seeker_id", $applier->id)->first();
        } elseif ($applier instanceof Company) {
            $applierWallet = Wallet::where("company_id", $applier->id)->first();
        } else {
            return $this->returnError("Invalid applier type.");
        }

        if (!$applierWallet) {
            return $this->returnError("Applier wallet not found.");
        }
        $applierWallet->balance += $service->price;
        $applierWallet->save();
        return $this->returnSuccess("Service marked as done and payment transferred successfully.");
    }
    public function messageWeb(Request $request)
    {
        return message($request, "web-customer");
    }

    public function messageApi(Request $request)
    {
        return message($request, "api-customer");
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


