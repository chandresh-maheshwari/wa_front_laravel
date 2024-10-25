<?php

namespace App\Http\Controllers;

use App\Models\PackagesModel;
use Illuminate\Http\Request;

class PackageController extends Controller
{

    public function create()
    {
        return view('package.create');
    }

    public function store(Request $request)
    {

        $data = $request->all();
        // dd($data);

        $package = new PackagesModel();

        if (isset($data['package_title'])) {
            $package->package_title = $data['package_title'];
        } else {

            $package->package_title = 'Default Title';
        }

        if (isset($data['package_des'])) {
            $package->package_des = $data['package_des'];
        } else {
            $package->package_des = 'Default Description';
        }

        if (isset($data['package_price'])) {
            $package->package_price = $data['package_price'];
        } else {
            $package->package_price = 0;
        }

        if (isset($data['additional_info'])) {
            $package->additional_info = $data['additional_info'];
        } else {
            $package->additional_info = 0;
        }

        $package->save();

        return response()->json([
            'success' => true,
            'message' => 'Package Saved successfully',
        ]);
    }

    // public function store(Request $request) {

    //     $data = $request->all();

    //     $package = new PackagesModel();
    //     $package->package_title = $data['package_title'];
    //     $package->package_des = $data['package_des'];
    //     $package->package_price = $data['package_price'];
    //     $package->additional_info = $data['additional_info'];
    //     $package->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Testimonial saved successfully',
    //     ]);
    // }

    // public function package_list()
    // {

    //     return view('package.index');
    // }

    public function package_list()
    {

        return PackagesModel::all();
    }

    public function getClient($id)
    {
        // Fetch the client data by ID
        $data = PackagesModel::findOrFail($id);

        // Return the client data as a JSON response
        return response()->json($data);
    }

    public function listing(Request $request)
    {

        $render_array = array();
        $draw = $request->input('sEcho');
        $row = $request->input('iDisplayStart');
        $rowperpage = $request->input('iDisplayLength');
        $indexColumn = $_POST['iSortCol_0'];
        $columnName = $_POST['mDataProp_' . $indexColumn];
        $columnSortOrder = $_POST['sSortDir_0'];
        $searchValue = $request->input('sSearch');

        $users = PackagesModel::getpackagedata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        $totalRecordwithFilter = PackagesModel::getpackageTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        //  dd($totalRecordwithFilter);
        $render_array['PackagesModel'] = $users;

        $data = array();
        if (count($render_array['PackagesModel']) > 0) {

            foreach ($render_array['PackagesModel'] as $key => $values) {

                $data[$key]['id'] = $values->idd;
                $data[$key]['package_title'] = $values->package_title;
                $data[$key]['package_des'] = $values->package_des;
                $data[$key]['package_price'] = $values->package_price;
                $data[$key]['additional_info'] = $values->additional_info;

            }
        }
        $output = array(
            "sEcho" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data,

        );

        return $output;
    }

    public function destroy(Request $request)
    {

        $multi_data = $request->deleteids_arr;

        $multi_data = $request->id;
        $multi_data2 = $request->deleteids_arr;
        if ($request->delete_type == 'multi') {
            $data = PackagesModel::whereIn('id', $multi_data2)
                ->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Packages Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else if ($request->delete_type == 'permanent') {
            $data = PackagesModel::whereIn('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Packages permanently Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else {
            // dd($request->id);
            //    $data = DB::table('roles')->where('id', $request->id)->delete();
            $data = PackagesModel::where('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Package Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        }

    }

    public function edit($id)
    {

        return view('package.edit', ['id' => $id]);

    }

    public function update(Request $request)
    {

        // dd($request);
        $package = PackagesModel::find($request->id);
        // dd($request);
        // dd($testimonial);
        return response()->json($package);
    }

    public function update_save(Request $request)
    {

        $package = PackagesModel::find($request->id);
        $package->package_title = $request->package_title;
        $package->package_des = $request->package_des;
        $package->package_price = $request->package_price;
        $package->additional_info = $request->additional_info;

        $package->update();

        return response()->json([
            'success' => true,
            'message' => 'Package Update successfully',
        ]);

    }

    public function destroydatas(Request $request)
    {

        $multi_data = $request->deleteids_arr;

        $multi_data = $request->id;
        $multi_data2 = $request->deleteids_arr;
        if ($request->delete_type == 'multi') {
            $data = PackagesModel::whereIn('id', $multi_data2)
                ->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Packages Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else if ($request->delete_type == 'permanent') {
            $data = PackagesModel::whereIn('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Packages permanently Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else {
            // dd($request->id);
            //    $data = DB::table('roles')->where('id', $request->id)->delete();
            $data = PackagesModel::where('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Package Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        }

    }

    public function destroy_all_data(Request $request, $ids)
    {
        try {
            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No IDs provided for deletion', 'code' => 400]);
            }

            // Convert the comma-separated string to an array of IDs
            $idArray = explode(',', $ids);

            // Perform the deletion
            $deletedRows = PackagesModel::whereIn('id', $idArray)->delete();

            if ($deletedRows > 0) {
                return response()->json(['status' => 'success', 'message' => 'Clients deleted successfully', 'code' => 200]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No matching clients found for deletion', 'code' => 404]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => 500]);
        }
    }

}
