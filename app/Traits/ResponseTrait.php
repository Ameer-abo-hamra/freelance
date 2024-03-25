<?php
namespace App\Traits;


trait ResponseTrait
{
    public function returnError($msgErorr = "", $errorNumber = 400)
    {

        return response()->json([
            "status" => false,
            "message" => $msgErorr,
            "statusNumber" => $errorNumber
        ]);

    }
    public function returnSuccess($msgSuccess = "", $succesNumber = 200)
    {

        return response()->json([
            "status" => true,
            "message" => $msgSuccess,
            "statusNumber" => $succesNumber
        ]);

    }

    public function returnData($msgData = "", $key, $data = [], $responseNumber = 200)
    {
        return response()->json([
            "status" => true,
            "message" => $msgData,
            "statusNumber" => $responseNumber,
            "$key" => $data,
        ]);
    }
    public function returnValidate()
    {

        return response()->json([
            "status" => false,
            "message" => "username or password is not correct",
            "errorNumber" => "000",
        ]);

    }

}
