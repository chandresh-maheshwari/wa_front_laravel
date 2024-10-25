<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Product;

class OrderController extends Controller
{
    public function paymentmethod(Request $request)
    {        
        // dd($request->all());
        // dd(session()->all());
        // $sattionall = session()->all();

        $useridget = Session::get('userid');
        $userid = $useridget;
        $products = Session::get('orderdetails');

        // dd($products);
    
        foreach ($products as $qutkey => $orderdata) {
            if (is_array($orderdata)) {
                $data = array();
                $data[$qutkey]['productid'] =  $orderdata['productid'];
                $data[$qutkey]['product_name'] =  $orderdata['title'];
                $data[$qutkey]['price'] =  $orderdata['price'];
                $data[$qutkey]['quantity'] =  $orderdata['qty'];
                $data[$qutkey]['total'] =  $orderdata['qty'] * $orderdata['price'];
                $data[$qutkey]['subtotal'] = $products['total'];
                $data[$qutkey]['userid'] = $userid;
                $data[$qutkey]['created_at']= date('Y-m-d H:i:s');
                $data[$qutkey]['updated_at'] = date('Y-m-d H:i:s');
                $store = OrderModel::insert($data);
                $id[]= \DB::getPdo()->lastInsertId();
            }
            // dd($id);
        }        
        return view('paymentMethod',['lastinsertedid'=>$id]);
    }

    public function cardinformation(Request $request){
        
        // dd($request->all());
        $products = Session::get('addressaccinfo');

        // dd($products);

        // $data = OrderModel::get('id');
        $getdata['card_id'] = 0;
        $getdata['university_student'] = $products['university_student'];
        $getdata['instructions'] = $products['instructions'];
        $getdata['walk_up'] = $products['walk_up'];
        $getdata['health_protection'] = $products['health_protection'];
        $getdata['payment_method'] = $request['selectcard'];

        $getdata['card_number'] = $request['card_information'];
        $getdata['expiree_date'] = $request['expireedate'];
        $getdata['csv'] = $request['cvc_number'];
        $getdata['status'] = 0;
        $getdata['is_deleted'] = 0;        
        $alldata = OrderPaymentModel::create($getdata);
        // $alldata->save();
        // dd($alldata);
        $lastdata = $alldata->id;
        // dd($lastdata);

// dd($request->lastinsertedid);

        foreach($request->lastinsertedid as $keys => $cartid){
        // $cartUpdate= OrderModel::update(['order_id', $lastdata])->where('id',$cartid);

        $query_update =  \DB::table('cart_table')
   ->where('id', $cartid)
   ->update(['order_id' => $lastdata]);
    }

// dd($query_update);



            // $alldataget =([
            //     $alldata->id,
            //     $alldata->university_student,
            //     $alldata->instructions,
            //     $alldata->walk_up,
            //     $alldata->health_protection,
            //     $alldata->payment_method,
            //     $alldata->expiree_date,
            //     $alldata->csv,
            //     $alldata->status,
            //     $alldata->is_deleted,
            // ]);
            // dd($alldataget);
        //     return view('paymentMethod');
        // $order_id = [
        //                 'order_id' => $alldata->id,
        //             ];
         
        //  $data = $request->only('order_id');
         
        //  $plansubmission = OrderModel::where('id')->update($data);
        // //  dd($plansubmission);
    // $plansubmission->update($data);
    }

}
