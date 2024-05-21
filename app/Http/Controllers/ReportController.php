<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job_seeker;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function report($reporter_id, $reported_id)
    {
        $company = Company::find($reporter_id);
        $job_seeker = Job_seeker::find($reported_id);
        $company->reportsMade()->create([
            "reason" => "aa",
            "reported_type"=>"ss",
            "reported_id"=>1
        ]);

    }
}
