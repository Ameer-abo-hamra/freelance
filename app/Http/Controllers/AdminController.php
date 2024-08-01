<?php

namespace App\Http\Controllers;

use App\Events\ForTesting;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Job_seeker;
use App\Models\Post;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;
use App\Models\Report;
use App\Models\Service;
use App\Http\Resources\UserProfileResource;
use App\Models\Admin;
use Validator;
use App\Http\Requests\UpdateAdminProfileRequest;


class AdminController extends Controller
{
    use ResponseTrait;
    public function deleteUser($type, $id)
    {
        if ($type == "company") {
            $company = Company::find($id);
            if ($company) {
                $company->delete();
                // broadcast(new ForTesting("you deleted company"));
                return $this->returnSuccess("done");
            }
            return $this->returnError("check id :)");
        } elseif ($type == "jobseeker") {
            $job_seeker = Job_seeker::find($id);
            if ($job_seeker) {
                $job_seeker->delete();
                return $this->returnSuccess("done");
            }
            return $this->returnError("check id :)");
        } elseif ($type == "customer") {
            $customer = Customer::find($id);
            if ($customer) {
                $customer->delete();
                return $this->returnSuccess("done");
            }
            return $this->returnError("check id :)");
        }

        return $this->returnError("check the type that you send it ");
    }

    public function showAllReports()
    {
        $reports = Report::with(['reporter', 'reported'])->get();

        $formattedReports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'reporter' => [
                    'id' => $report->reporter->id,
                    'type' => $report->reporter_type,
                    'reporter_name' => $report->reporter->full_name ?? $report->reporter->name,
                ],
                'reported' => [
                    'id' => $report->reported->id,
                    'type' => $report->reported_type,
                    'reproted_name/title' => $report->reported->full_name ?? $report->reported->name ?? $report->reported->title,
                ],
                'reason' => $report->reason,
            ];
        });

        return $this->returnData("all  reports", "reports", $formattedReports);
    }

    public function showAllServices()
    {
        $services = Service::with(['customer', 'skills'])->get();

        return $this->returnData("all services", "services", $services);
    }

    public function updateServiceStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_accepted' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $service = Service::find($id);

        if (!$service) {
            return $this->returnError("Service not found");
        }

        $service->is_accepted = $request->is_accepted;
        $service->save();

        return $this->returnSuccess("Service status updated successfully");
    }

    public function profile($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return $this->returnError("admin not found");
        }

        $profile = [
            'full_name' => $admin->full_name,
            'birthOfDate' => $admin->birthOfDate,
            'city' => $admin->city,
            'profilePhoto' => $admin->profilePhoto,
        ];

        return $this->returnData("", "profile:", $profile);
    }
    public function updateProfile(UpdateAdminProfileRequest $request)
    {
        $admin = Admin::find($request->id);

        if (!$admin) {
            return $this->returnError("admin not found");
        }

        $admin->fill($request->only(['full_name', 'birthOfDate', 'city']));

        if ($request->hasFile('profilePhoto')) {
            $file = $request->file('profilePhoto');
            $path = $file->store('profile_photos', 'public');
            $admin->profilePhoto = $path;
        }

        $admin->save();

        return $this->returnSuccess("Profile updated successfully");

    }

}


