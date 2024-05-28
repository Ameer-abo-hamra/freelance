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
    public function report(Request $request)
    {
        $validator = validator::make($request->all(), [
            "reported_id" => "required",
            "reported_type" => "required|string",
            "reporter_id" => "required",
            "reporter_type" => "required",
            "reason" => "required"
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        // find the reported entity:
        if ($request->reported_type == "Job_seeker") {
            $reported = Job_seeker::find($request->reported_id);
        } elseif ($request->reported_type == "Company") {
            $reported = Company::find($request->reported_id);
        } elseif ($request->reported_type == "Customer") {
            $reported = Customer::find($request->reported_id);
        }
        if ($request->reported_type == "Post") {
            $reported = Post::find($request->reported_id);
        }
        //find the person who report:

        if ($request->reporter_type == "Job_seeker") {
            $reporter = Job_seeker::find($request->reporter_id);
        } elseif ($request->reporter_type == "Company") {
            $reporter = Company::find($request->reporter_id);
        } elseif ($request->reporter_type == "Customer") {
            $reporter = Customer::find($request->reporyter_id);
        }

        if (!$reported || !$reporter) {
            return $this->returnError("something went wrong");
        }

        $report = new Report();
        $report->reporter()->associate($reporter);
        $report->reported()->associate($reported);
        $report->create([
            "reason" => $request->reason
        ]);
        $report->save();
    }
}
