<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\ProdutPromocodeModel;
use App\Models\PromocodeModel;
use Illuminate\Http\Request;
use Product;

class PromocodeController extends Controller
{
    public function index()
    {
        // $product_data = ProductModel::get();
        // return view('dashboard.promocode_add', ['data' => $product_data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function promocode_add()
    {
        $product_data =  ProductModel::where([['is_deleted', '=', 0], ['title', '!=', null]])->get()->sortByDesc("id");
        $product_data_cn =  ProductModel::where([['is_deleted', '=', 0], ['title_cn', '!=', null]])->get()->sortByDesc("id");
        // dd($product_data_cn);
        // return view('dashboard.promocode_add',['data' => $product_data,
        //                                         // 'data_cn' => $product_data_cn]);
        return View('dashboard.promocode_add')->with([
            'data' => $product_data,
            'data_cn' => $product_data_cn,
        ]);
    }

    public function promocode_add_data(Request $request)
    {
        // dd( $request->all()); 
        $period_to = $request->period_to;
        $period_by = $request->period_by;
        if ($period_to == null or $period_by == null) {
            $period_to == null;
            $period_by == null;
        } else {
            $period_to = date("Y-m-d", strtotime($period_to));
            $period_by = date("Y-m-d", strtotime($period_by));
        }
        $period_to_cn = $request->period_to_cn;
        $period_by_cn = $request->period_by_cn;
        if ($period_to_cn == null or $period_by_cn == null) {
            $period_to_cn = null;
            $period_by_cn = null;
        } else {
            $period_to_cn = date("Y-m-d", strtotime($period_to_cn));
            $period_by_cn = date("Y-m-d", strtotime($period_by_cn));
        }

        $promocode_val = [
            'code' => $request->code,
            'name' => $request->name,
            'period_by' => $period_by,
            'period_to' => $period_to,
            // 'product_dropdown' => $request->product_dropdown,

            'code_cn' => $request->code_cn,
            'name_cn' => $request->name_cn,
            'period_by_cn' => $period_by_cn,
            'period_to_cn' => $period_to_cn,
            'is_deleted' => 0,

        ];
        $request['is_deleted'] = 0;
        // dd($promocode_val); die();
        $promocode_All_data = PromocodeModel::create($promocode_val);
        $id = \DB::getPdo()->lastInsertId(); //fething last instrted id from procode table
        // $request->id=$id;

        $data = $request->product_dropdown;
        $price = $request->price;

        $data_cn = $request->product_dropdown_cn;
        $price_cn = $request->price_cn;

        if (isset($data, $price)) {

            $values = array_combine($data, $price);
            // dd($values);
            foreach ($values as $value => $val) {

                // foreach(($data as $value) AND ($price as $val)){
                // dd($value);
                $request->product_name  = $value;
                // foreach($price as $val){
                $request->price = $val;

                $product_promocode_val = [
                    'promocode_id' => $id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'product_name' => $request->product_name,
                    'is_deleted' => 0,

                ];
                // } 
                $product_promocode_All_data = ProdutPromocodeModel::create($product_promocode_val);
            }
        }

        if (isset($data_cn, $price_cn)) {

            $values = array_combine($data_cn, $price_cn);
            // dd($values);
            foreach ($values as $value => $val) {
                $request->product_name  = $value;
                $request->price = $val;
                $product_promocode_val = [
                    'promocode_id' => $id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'product_name' => $request->product_name,
                    'is_deleted' => 0,

                ];
                // } 
                $product_promocode_All_data = ProdutPromocodeModel::create($product_promocode_val);
            }
        }   
        if($request->code !== null){
            return redirect('/promocode_datatable');
        }
        else{
            return redirect('/promocode_datatable/cn');
        }
    }


    public function promocode_update(Request $request, $id)
    {
        // dd($id);
        // $g = \DB::table('product')->where('id','=', $id);
        $Promocode_selected_row = \DB::table('promocode')->where('id', '=', $id)->where('is_deleted', '=', 0)->first();
        $product_promocode_rows = \DB::table('product_promocode')->where('promocode_id', '=', $id)->where('is_deleted', '=', 0)->get();

        // dd($product_promocode_rows);

        // dd($Promocode_selected_row); die();
        if(isset($Promocode_selected_row->period_to)){
            $period_to = date("m-d-Y", strtotime($Promocode_selected_row->period_to));
        }
        else{
            $period_to = null;
        }
        if(isset($Promocode_selected_row->period_by)){
            $period_by = date("m-d-Y", strtotime($Promocode_selected_row->period_by));
        }
        else{
            $period_by = null;
        }

        if(isset($Promocode_selected_row->period_to_cn)){
            $period_to_cn = date("m-d-Y", strtotime($Promocode_selected_row->period_to_cn));
        }else{
            $period_to_cn = null;
        }
        if(isset($Promocode_selected_row->period_by_cn)){
            $period_by_cn = date("m-d-Y", strtotime($Promocode_selected_row->period_by_cn));
        }else{
            $period_by_cn = null;
        }

        $Promocode_selected_row->period_to = $period_to;
        $Promocode_selected_row->period_by = $period_by;    

        $Promocode_selected_row->period_to_cn = $period_to_cn;
        $Promocode_selected_row->period_by_cn = $period_by_cn;

        // dd($period_to);
        // return redirect('/product_update',['data'=>$users]); 
        // return redirect('promocode_update/',);
        // dd($Promocode_selected_row);
        // $all_product_list = \DB::table('product')->get()->where('is_deleted', 0);
        $all_product_list =  \DB::table('product')->where([['is_deleted', '=', 0], ['title', '!=', null]])->get()->sortByDesc("id");
        $all_product_list_cn =  \DB::table('product')->where([['is_deleted', '=', 0], ['title_cn', '!=', null]])->get()->sortByDesc("id");
        // dd($all_product_list_cn);
        return View('dashboard.promocode_update')->with([
            'data' => $Promocode_selected_row,
            'all_product_list' => $all_product_list,
            'all_product_list_cn' => $all_product_list_cn,
            'product_promocode_data' => $product_promocode_rows,
        ]);
        // return view('dashboard.promocode_update', ['data' => $Promocode_selected_row]);
        // dd($users);
    }

    public function update_promo(Request $request)
    {
        $id = $request->id;
        // dd($id);
        if(isset($request->period_to)){
            $period_to = date("Y-m-d", strtotime($request->period_to));
        }
        else{
            $period_to = null;
        }
        if(isset($request->period_by)){
            $period_by = date("Y-m-d", strtotime($request->period_by));
        }
        else{
            $period_by = null;
        }
        if(isset($request->period_to_cn)){
            $period_to_cn = date("Y-m-d", strtotime($request->period_to_cn));
        }
        else{
            $period_to_cn = null;
        }
        if(isset($request->period_by_cn)){
            $period_by_cn = date("Y-m-d", strtotime($request->period_by_cn));
        }
        else{
            $period_by_cn = null;
        }
        // $request->product_dropdown = implode(',', $request->product_dropdown);

        $update =  now()->format('Y-m-d h:i:s');
        // dd($update); die();
        $request['is_deleted'] = 0;
        // $request->code=$id;
        // $all_data = ProductModel::update($val);
        if ($request->file == null) {
            $update = \DB::table('promocode')->where('id', $id)->limit(1)->update([
                'code' =>  $request->code,
                'name' =>  $request->name,
                'period_by' =>  $period_by,
                'period_to' =>  $period_to,
                // 'product_dropdown' => $request->product_dropdown,
                'updated_at' => $update,

                'code_cn' =>  $request->code_cn,
                'name_cn' =>  $request->name_cn,
                'period_by_cn' =>  $period_by_cn,
                'period_to_cn' =>  $period_to_cn,
            ]);
            $delete = \DB::table('product_promocode')->where('promocode_id', $id)->update(['is_deleted' => 1]);
        }

        $data = $request->product_dropdown;
        $price = $request->price;

        $data_cn = $request->product_dropdown_cn;
        $price_cn = $request->price_cn;

        if (isset($data, $price)) {
            $values = array_combine($data, $price);
            foreach ($values as $value => $val) {
                // foreach(($data as $value) AND ($price as $val)){
                // dd($value);
                $request->product_name  = $value;
                // foreach($price as $val){

                $request->price = $val;

                $product_promocode_val = [
                    'promocode_id' => $id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'product_name' => $request->product_name,
                    'is_deleted' => 0,

                ];
                // } 
                $product_promocode_All_data = ProdutPromocodeModel::create($product_promocode_val);
            }
        }
        if (isset($data_cn, $price_cn)) {
            $values = array_combine($data_cn, $price_cn);
            foreach ($values as $value => $val) {

                // foreach(($data as $value) AND ($price as $val)){
                // dd($value);
                $request->product_name  = $value;
                // foreach($price as $val){

                $request->price = $val;

                $product_promocode_val = [
                    'promocode_id' => $id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'product_name' => $request->product_name,
                    'is_deleted' => 0,

                ];
                // } 
                $product_promocode_All_data = ProdutPromocodeModel::create($product_promocode_val);
            }
        }
        if($request->code !== null){
            return redirect('/promocode_datatable');
        }
        else{
            return redirect('/promocode_datatable/cn');
        }
    }


    public function promocode_delete($id)
    {
        //
        $delete = \DB::table('promocode')->where('id', $id)->limit(1)->update(['is_deleted' => 1]);
        $delete = \DB::table('product_promocode')->where('promocode_id', $id)->update(['is_deleted' => 1]);

        return redirect('/promocode_datatable');
    }
    public function promocode_datatable($lang = 'en')
    {
        $promocode = PromocodeModel::all()->sortByDesc("id")->where('is_deleted', 0);
        // dd($promocode);
        // $product = ProductModel::all()->sortByDesc("id")->where('is_deleted' , 0);

        if ($lang == 'cn') {
            return view('dashboard.promocode_dt_cn', ['data' => $promocode]);
        } else {
            return view('dashboard.promocode_dt', ['data' => $promocode]);
        }
    }
}
