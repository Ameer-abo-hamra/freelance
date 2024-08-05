<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Job_seeker;
use Illuminate\Http\Request;
use App\Models\Post;
use Validator;
use App\Traits\ResponseTrait;
use App\Models\Report;

class ReportController extends Controller
{
    use ResponseTrait;
    protected $adminController;

    public function report(Request $request)
    {
        $validator = validator::make($request->all(), [
            "reported_id" => "required",
            "reported_type" => "required|string",
            "reporter_id" => "required",
            "reporter_type" => "required|string",
            "reason" => "required"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        // find the reported entity:
        if ($request->reported_type == "job_seeker") {
            $reported = Job_seeker::find($request->reported_id);
        } elseif ($request->reported_type == "company") {
            $reported = Company::find($request->reported_id);
        } elseif ($request->reported_type == "customer") {
            $reported = Customer::find($request->reported_id);
        } elseif ($request->reported_type == "post") {
            $reported = Post::find($request->reported_id);
        }
        //find the person who report:

        if ($request->reporter_type == "job_seeker") {
            $reporter = Job_seeker::find($request->reporter_id);
        } elseif ($request->reporter_type == "company") {
            $reporter = Company::find($request->reporter_id);
        } elseif ($request->reporter_type == "customer") {
            $reporter = Customer::find($request->reporyter_id);
        }

        if (!$reported || !$reporter) {
            return $this->returnError("something went wrong");
        }


        $report = new Report();
        $report->reporter()->associate($reporter);
        $report->reported()->associate($reported);
        $report->reason = $request->reason;
        $report->save();
        if ($request->reported_type == "post") {
            return $this->checkAndDeletePost($request->reported_id);
        }
        return $this->returnSuccess("Report submitted successfully.");

    }
    protected function checkAndDeletePost($post_id)
    {
        $reportController = app()->make(ReportController::class);
        $reportCount = $reportController->countReports($post_id);
        if ($reportCount >= 10) {
            $postController = app()->make(PostController::class);
            return $postController->deletePostDirectly($post_id);
        }

        return $this->returnSuccess("Report submitted successfully, but post not deleted as reports are less than 50");
    }

    public function countReports($post_id)
    {
        $type = "App\Models\Post";
        $count = Report::where("reported_type", $type)->where("reported_id", $post_id)->count();
        if ($count) {
            return $count;
        }
    }
}
