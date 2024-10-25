<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function add(Request $req)
    {
        $device = new users;
        $device->name = $req->name;
        $device->email = $req->email;
        // $device->password = $req->password;
        $result = $device->save();
        if ($result) {
            return ["result" => "data rtjhfgnjn rtyhbn cnyrn tyhj"];
        } else {
            return ["result" => "hardik ghfgjhfgjhnb yjhnfggchjdt"];
        }
    }
}