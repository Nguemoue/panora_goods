<?php

namespace App\Http\Controllers;

class EmployeeVerification
{
    public function __invoke(){
        return view('employee-verification');
    }
}
