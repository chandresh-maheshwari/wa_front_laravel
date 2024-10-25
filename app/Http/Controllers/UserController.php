<?php

namespace App\Http\Controllers;

use App\Models\AccountInformationModel;
use App\Models\AddressAccountInformationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\ProductModel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('user');
    }

    public function index()    {       
        AccountInformationModel::destroy('user');
        return view('homepage');
    }    

    public function userlogout(Request $request)
    {
        Session::flush();
        return redirect('/signin');
    }
}
