<?php

namespace App\Http\Controllers;

use AddressAccountInformation;
use App\Models\AccountInformationModel;
use App\Models\AddressAccountInformationModel;
use App\Models\ProductModel;
use App\Models\ProdutPromocodeModel;
use App\Models\PromocodeModel;
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

    public function addaccountinformation(Request $request)
    {
        // dd($request->all());
       $adddata = ([
           'name' => $request->name,
           'email' => $request->email,
           'password' =>Hash::make($request->password),
       ]);

             
       $data = AccountInformationModel::create($adddata);       
      
       $addressaccinfid = $data['id'];     
       $request['userid'] = $addressaccinfid;
       $addressaccinfo =([  
           'userid' => $addressaccinfid,      
           'address' => $request->address,
           'city' => $request->city,
           'states'=>$request->states,
           'contact' => $request->contact,
           'pickup_address' => ($request->pickup_address == 'no')? $request->newaddress : $request->pickup_address,
           'date1' => $request->date1,
           'time1' => $request->time1,
           'date2' => $request->date2,
           'time2' => $request->time2, 
           'date3' => $request->date3,
           'time3' => $request->time3,
        ]); 
        $addressaccinfoadd = AddressAccountInformationModel::create($addressaccinfo);
        // dd($addressaccinfoadd);

        if($this->loginuser($request) && Session::has('userid')){
            return view('address');
        }else if(Session::has('userid')){
            return redirect('/');
        }else{
            return redirect('signin');
        }


   }   

    public function pricing()
    {
        $user = ProductModel::get();
        return view('pricing', ['data' => $user]);
    }

    public function pricechange(Request $request){

        // dd($request->all());

        if ($request->month >= 6 ) {

            $data = ProductModel::pluck('price_per_month_6above','id');
            return ['success' => true, 'data' => $data];
        
         } else if($request->month < 6 ){
            
            $data2 =  ProductModel::pluck('price_per_month_6below','id');
            return ['success' => true, 'data' => $data2];
                  
        }
    }

    public function promoverify(Request $request){
        //  dd(888888);
        $data = PromocodeModel::where('code','=',$request->code)->first();
        //    echo "<pre>";
        //    print_r($data);
        //    exit;
        if ($data == NUll) {            
            
            return ['error' => false, 'massage' => 'invalid promocode'];
        
         } else if($data !== NUll){
            
            $havepromocode =  $data->id;
            // dd($havepromocode);
            
            $matchdata = ProdutPromocodeModel::get()->where('promocode_id','=',$havepromocode);

            // dd($matchdata);
            foreach($matchdata as $newmatchdata){               
            
                $price[] =[
                    $newmatchdata->price,
                    $newmatchdata->promocode_id,
                    $newmatchdata->product_name,
                ];
            }
            // dd($price);
            // exit;
            return ['sucess' => true, 'priceget' => $price];
                  
        }
    }

    public function address(Request $request)
    {   
        // dd($request->all());
        $promo = $request->promoprice;
        $qtyArr = $request->sm;
        // dd($qtyArr);
        foreach($qtyArr as $qtyKey => $qty){

        
            if($qty > 0){
                $dataObj[$qtyKey]['title'] = $request->titleget[$qtyKey];
                $dataObj[$qtyKey]['price'] = $request->priceget[$qtyKey];
                $dataObj[$qtyKey]['qty'] = $qty;
                $dataObj[$qtyKey]['productid'] = $request->productid[$qtyKey];
            }
        }
        $dataObj['promo'] = $promo;
        $dataObj['total'] = $request->hidden_totalamount;
        
        // echo '<pre>';
        // print_r($dataObj);
        // exit;    
       
        Session::put('orderdetails', $dataObj);    

        if(Session::has('userid')){            
            return view('address');
        }else{
            return view('register',['orderprocess' => 'yes']);
        }
    }

    public function orderconfirm(Request $request)
    {       

        // dd($request->all());
        $userid = Session::get('userid');
        // dd(Session::get('userid'));
        $addressaccinfo =([  
            'userid' => $userid,    
            'university_student' => $request->university_student,
            'instructions' => $request->instructions,
            'walk_up'=>$request->walk_up,
            'health_protection' => $request->health_protection,
        ]); 
        // dd($addressaccinfo);

        Session::put('addressaccinfo',$addressaccinfo);      
        // Session::get('addressaccinfo'); 
        // dd( Session::get('addressaccinfo'));
        
        // $data = UserAddressInformationModel::create($addressaccinfo);
        // dd($data);
        $accountinformation = AccountInformationModel::where('id','=',Session::get('userid'))->get();
        $checkinoutedetails = AddressAccountInformationModel::where('userid','=',Session::get('userid'))->get();
               
        // $orderdetailsdata = Session::get('orderdetails');
        // dd($checkinoutedetails);
        // return redirect()->redirect('orderconfirm');
        
        return view('orderConfirm',["accountinformation"=>$accountinformation], ["checkinoutedetails" => $checkinoutedetails]);

    }
    
}
