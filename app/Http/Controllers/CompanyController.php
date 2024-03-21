<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\Response;
use Validator;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use Response;
    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            "name" => "required|unique:companies| max:15",
            "employee_number" => "required |integer | min:10 | max:500000",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        Company::create([
            "name" => $request->name,
            "establishment_date" => $request->establishment_date,
            "employee_number" => $request->employee_number
        ]);

        return $this->returnSuccess("your data is saved successfully");
    }
}
