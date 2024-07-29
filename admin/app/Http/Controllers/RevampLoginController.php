<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class RevampLoginController extends Controller
{
    public function create(){
        return view('revamp.revamp_login');
    }
    public function dashboard(){
        return view('revamp.revamp_logout');
    }
    public function store(Request $request)
    {
        $Username = 'revamp';
        $Password = '12345678';
        if ($request->input('username') == $Username && $request->input('password') == $Password) {
            Session::put('key', '654646');
            return $this->authenticated();
        } else {
            return redirect()->to('revamp/login')->withErrors(trans('auth.failed'));
        }
    }
    protected function authenticated()
    {
        return redirect()->route('revamp.dashboard');
    }
    public function logout()
    {
        Session::forget('key');
        return redirect('revamp/login');
    }
}
