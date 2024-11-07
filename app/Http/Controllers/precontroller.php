<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class precontroller extends Controller
{
    public function add(Request $req)
    {
        $device = new User;
        $device->name = $req->name;
        $device->email = $req->email;
        $device->password = Hash::make($req->password);
        $result = $device->save();

        if ($result) {
            return ["Result" => "Data Has been insert"];
        } else {
            return ["Result" => "Data Has been not insert"];
        }
    }

    
    
    public function userlist()
    {
        return User::all();
    }

    public function userstore(Request $request)
    {

        $data = $request->all();
        $device = new User;
        $device->name = $request->name;
        $device->email = $request->email;
        $device->password = Hash::make($request->password);
        $result = $device->save();
        // $services->save();

        return response()->json([
            'success' => true,
            'message' => 'Services saved successfully',
        ]);
    }
}

