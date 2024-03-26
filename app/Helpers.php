<?php
use App\Mail\Customer;
use App\Mail\JobseekerMail;
use App\Mail\Company;
use Illuminate\Support\Str;

function makeCode($type, $email)
{
    $code = Str::random(6);
    if ($type == "customer") {
        Mail::to($email)->send(new Customer($code));
        return $code;

    } elseif ($type == "company") {
        Mail::to($email)->send(new Company($code));
        return $code;

    } else {
        Mail::to($email)->send(new Company($code));
        return $code;
    }
}
