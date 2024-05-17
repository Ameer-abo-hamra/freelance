<?php

namespace App\Http\Controllers;

use App\Events\ForTesting;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Job_seeker;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

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
        }
        elseif($type== "customer") {
            $customer = Customer::find($id);
            if ($customer) {
                $customer->delete();
                return $this->returnSuccess("done");
            }
            return $this->returnError("check id :)");
        }

        return $this->returnError("check the type that you send it ");
    }
}
