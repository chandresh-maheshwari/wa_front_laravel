<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use Illuminate\Http\Request;    

class AccountController extends Controller
{
    // Fetching data drom two table 
    public function account_list()
    {
        $account_data = AccountModel::join('address_account_information', 'account_information.id', '=', 'address_account_information.userid')
               ->get(['account_information.*', 'address_account_information.*']);
       
            return view('dashboard.account_list', ['data' => $account_data]);
      
    }
}
