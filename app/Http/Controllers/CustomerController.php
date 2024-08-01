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
use App\Helpers;

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
        $validator = validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'body' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $post = Post::find($post_id);
        if (!$post) {
            return $this->returnError("post not found");
        }

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return $this->returnError("customer not found");
        }

        $comment = new Comment();
        $comment->body = $request->body;
        $comment->commentable_type = Customer::class;
        $comment->commentable_id = $customer->id;
        $comment->post_id = $post_id;

        $comment->save();

        return $this->returnSuccess("comment added successfully");
    }

    // public function updateComment(Request $request, $comment_id)
    // {
    //     $validator = validator::make($request->all(), [
    //         'body' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->returnError($validator->errors()->first());
    //     }

    //     $comment = Comment::find($comment_id);
    //     if (!$comment) {
    //         return $this->returnError("Comment not found");
    //     }

    //     $comment->body = $request->input('body');
    //     $comment->save();

    //     return $this->returnSuccess("comment updated successfully");
    // }


}


