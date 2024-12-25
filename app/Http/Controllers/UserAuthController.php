<?php

namespace App\Http\Controllers;

use AddressAccountInformation;
use App\Models\AccountInformationModel;
use App\Models\AddressAccountInformationModel;
use App\Models\UserAddressInformationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserAuthController extends Controller
{
    public function loginuser(Request $request ){

        $email = $request->input('email');
        $password = $request->input('password');
       //  dd($password);
        $user = AccountInformationModel::where('email', '=', $email)->first();
        if(!$user AND $user == null) {
           return back()->withErrors(
               ['email' => 'The provided credentials do not match our records.']
           );
        }
        elseif(!Hash::check($password, $user->password)) {
           return back()->withErrors(
               ['password' => 'The password is incorrect.']
           );
        }    
        Session::put('userid', $user->id);    

        if(!isset($request->direct_login) || $request->direct_login == 'no'){
            return redirect('/');  
        }else if($request->direct_login == 'yes'){
            return view('address');
        }else if($request->direct_register == 'no'){
            return true;
        }else if($request->direct_register == 'yes'){
            return false;
        }    
    
    }

    

   

 

  

  

   
    
}
