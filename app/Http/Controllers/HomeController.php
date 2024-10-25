<?php

namespace App\Http\Controllers;

use AccountInformation;
use App\Models\AccountInformationModel;
use App\Models\ProductModel;
use GuzzleHttp\Promise\Create;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Product;

class HomeController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }
}
