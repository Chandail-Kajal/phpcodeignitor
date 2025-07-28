<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('add-employee');
    }

    public function login(): string{
        return view('login');
    }
    public function dashboard(): string{
        return view('dashboard');
    }
}
