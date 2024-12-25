<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('user');
    }

    // public function index()    {       
    //     AccountInformationModel::destroy('user');
    //     return view('homepage');
    // }    

    public function userlogout(Request $request)
    {
        Session::flush();
        return redirect('/signin');
    }
}
