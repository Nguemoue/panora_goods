<?php

namespace App\Http\Controllers;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store()
    {
        return redirect()->back();
    }
}
