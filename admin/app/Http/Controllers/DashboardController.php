<?php

namespace App\Http\Controllers;

class DashboardController extends CoreInfController
{
    public function index(Type $var = null)
    {
        return view('home/dashboard');
        // dd('sd');
    }
}
