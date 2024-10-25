<?php

namespace App\Http\Controllers;

use App\Models\ServiceModel;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    //

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {

        $data = $request->all();
        $services = new ServiceModel();
        $services->service_title = $data['service_title'];
        $services->description = $data['description'];
        $services->save();

        return response()->json([
            'success' => true,
            'message' => 'Services saved successfully',
        ]);
    }

    public function services_list()
    {

        // $testimonial = TestimonialModel::all();
        //  dd($testimonial);

        return view('services.index');
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

        $users = ServiceModel::getservicedata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        $totalRecordwithFilter = ServiceModel::getserviceTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        //  dd($totalRecordwithFilter);
        $render_array['ServiceModel'] = $users;

        $data = array();
        if (count($render_array['ServiceModel']) > 0) {

            foreach ($render_array['ServiceModel'] as $key => $values) {

                $data[$key]['id'] = $values->idd;
                $data[$key]['service_title'] = $values->service_title;
                $data[$key]['description'] = (!empty($values->description) ? $values->description : '--');
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
            $data = ServiceModel::whereIn('id', $multi_data2)
                ->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Services Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else if ($request->delete_type == 'permanent') {
            $data = ServiceModel::whereIn('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Services permanently Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else {
            // dd($request->id);
            //    $data = DB::table('roles')->where('id', $request->id)->delete();
            $data = ServiceModel::where('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Services Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        }
    }

    public function edit($id)
    {
        $data = ServiceModel::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $services = ServiceModel::find($request->id);
        return response()->json($services);
    }

    public function update_save(Request $request, $id)
    {

        $services = ServiceModel::find($request->id);
        $services->service_title = $request->service_title;
        $services->description = $request->description;
        $services->update();
        return response()->json([
            'success' => 200,
            'message' => 'Services Update successfully',
        ]);
    }

    public function Servicelist()
    {
        return ServiceModel::all();
    }



    public function updates(Request $request, $id)
    {
        $request->validate([
            "service_title" => "required|max:191",
            "description" => "required|max:191",
        ]);

        $services = ServiceModel::find($id);

        if ($services) {

            $services->service_title = $request->service_title;
            $services->description = $request->description;
            $services->update();
            return response()->json(['message' => 'product added successfully'], 200);
        } else {
            return response()->json(['message' => 'no product found'], 404);
        }
    }






    public function hardik(Request $request, $id)
    {
       

        $services = ServiceModel::find($id);

        

            $services->service_title = $request->service_title;
            $services->description = $request->description;
            $services->update();
            return response()->json([
                'success' => 200,
                'message' => 'Services Update successfully',
            ]);
        
    }



    public function destroydata(Request $request, $ids)
    {

        // dd($request->all());
        try {
            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No IDs provided for deletion', 'code' => 400]);
            }
    
            // Convert the comma-separated string to an array of IDs
            $idArray = explode(',', $ids);
    
            // Perform the deletion
            $deletedRows = ServiceModel::whereIn('id', $idArray)->delete();
    
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
