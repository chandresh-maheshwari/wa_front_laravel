<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;
use mysqli;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function product_add(Request $request)
    {
        // dd($request->all());

        // Save the file locally in the storage/public/ folder under a new folder named /product
        // $request->file->store('product', 'public');

        $filename = $request->file;
        if (isset($filename)) {

            $orignalfilename = $filename->getClientOriginalName();
            $s = $request->file('file')->move(public_path('images'), $orignalfilename);
        } else {
            $orignalfilename = null;
        }

        $filename_cn = $request->file_cn;
        if (isset($filename_cn)) {

            $orignalfilename_cn = $filename_cn->getClientOriginalName();
            $s = $request->file('file_cn')->move(public_path('images'), $orignalfilename_cn);
        } else {
            $orignalfilename_cn = null;
        }
        // $file = time().'.'.$request->file->extension();        // $imageName = time().'.'.$request->file;
        // $imageName = $request->file;
        // dd($imageName);
        // dd($s);
        // $filename = time().'_'.$file->getClientOriginalName();
        // $filename = uniqid() . $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();

        // $location =  public_path('/images');
        // $file->move($location,$filename);
        // $filename_cn = $request->file_cn;
        //     $orignalfilename_cn = $filename_cn->getClientOriginalName();
        //     $s = $request->file('file')->move(public_path('images'), $orignalfilename_cn);

        $val = [
            'title' => $request->title,
            'file' => $orignalfilename,
            'storage' => $request->storage,
            'price_per_month_6above' => $request->price_per_month_6above,
            'price_per_month_6below' => $request->price_per_month_6below,
            'free_insurance' => $request->free_insurance,
            'charge' => $request->charge,
            'object' => $request->object,
            'time' => $request->time,
            'description' => $request->description,
            // 'created_at' => $request->description,
            // 'updated_at' => $request->description,
            'is_deleted' => 0,


            'title_cn' => $request->title_cn,
            'file_cn' => $orignalfilename_cn,
            'storage_cn' => $request->storage_cn,
            'price_per_month_6above_cn' => $request->price_per_month_6above_cn,
            'price_per_month_6below_cn' => $request->price_per_month_6below_cn,
            'free_insurance_cn' => $request->free_insurance_cn,
            'charge_cn' => $request->charge_cn,
            'object_cn' => $request->object_cn,
            'time_cn' => $request->time_cn,
            'description_cn' => $request->description_cn,

        ];
        // dd($val);
        $request['is_deleted'] = 0;
        $all_data = ProductModel::create($val);

        if($request->title !== null){
            // Alert::message('Location data entered succesfully!');
            // Alert::success('Success', 'You\'ve Successfully Registered');
            // return back();

            // return redirect('/product_list')->with(["title" => "Success", "code" => "200" , "message" => "Data submited"]);
            // return redirect()->route('/product_list')->with('update', 'Content has been updated successfully!');
            return redirect('/product_list'); 
            // return redirect()->back()->with('alert', 'Deleted!');
        }
        else{
            return redirect('/product_list/cn');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        // dd($request->all());
        // dd($request->file);

        $id = $request->id;
        $request['is_deleted'] = 0;
        $update =  now()->format('Y-m-d h:i:s');

        // $all_data = ProductModel::update($val);

        if ($request->file == null) {
            $orignalfilename = $request->old_file;
        } else {
            $filename = $request->file;
            $orignalfilename = $filename->getClientOriginalName();
            $s = $request->file('file')->move(public_path('images'), $orignalfilename);
        }

        if ($request->file_cn == null) {
            $orignalfilename_cn = $request->old_file_cn;
        } else {
            $filename_cn = $request->file_cn;
            $orignalfilename_cn = $filename_cn->getClientOriginalName();
            $s = $request->file('file_cn')->move(public_path('images'), $orignalfilename_cn);
        }



        // if ($request->file == null) {
        $update = \DB::table('product')->where('id', $id)->limit(1)->update([
            'title' =>  $request->title,
            'file' => $orignalfilename,
            'storage' =>  $request->storage,
            'price_per_month_6above' =>  $request->price_per_month_6above,
            'price_per_month_6below' =>  $request->price_per_month_6below,
            'free_insurance' =>  $request->free_insurance,
            'charge' =>  $request->charge,
            'object' =>  $request->object,
            'time' =>  $request->time,
            'description' =>  $request->description,
            'updated_at' => $update,

            'title_cn' => $request->title_cn,
            'file_cn' => $orignalfilename_cn,
            'storage_cn' => $request->storage_cn,
            'price_per_month_6above_cn' => $request->price_per_month_6above_cn,
            'price_per_month_6below_cn' => $request->price_per_month_6below_cn,
            'free_insurance_cn' => $request->free_insurance_cn,
            'charge_cn' => $request->charge_cn,
            'object_cn' => $request->object_cn,
            'time_cn' => $request->time_cn,
            'description_cn' => $request->description_cn,
        ]);

        if($request->title !== null){
            return redirect('/product_list');
        }
        else{
            return redirect('/product_list/cn');
        }
    }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function product_update(Request $request, $id)
    {
        // dd($id);
        // $g = \DB::table('product')->where('id','=', $id);
        $users = \DB::table('product')->where('id', '=', $id)->first();
        // return redirect('/product_update',['data'=>$users]);

        // dd($users);
        return view('dashboard.product_update', ['data' => $users]);


        // dd($users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function product_delete($id)
    {
        // Delete table row
        // dd($id);
        // $UpdateDetails = product::where('is_deleted', $id)->firstOrFail();
        $delete = \DB::table('product')->where('id', $id)->limit(1)->update(['is_deleted' => 1]);
        
        // return redirect('/product_list');
        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function product_list($lang = 'en')
    {
        // Delete table row
        // dd($lang);
        // $UpdateDetails = product::where('is_deleted', $id)->firstOrFail();
        $data = ProductModel::all()->sortByDesc("id")->where('is_deleted', 0);
        if ($lang == 'cn') {
            return view('dashboard.product_list_cn', ['data' => $data]);
        } else {
            return view('dashboard.product_list', ['data' => $data]);
        }
    }
}
