<?php

namespace App\Http\Controllers;

use App\Models\ClientModel;
use Illuminate\Http\Request;

class ClientController extends Controller
{

// nikunj code  (08/11/2023)
    public function index()
    {
        $clientmodel = ClientModel::all();

        // return json format
        return response()->json([
            'results' => $clientmodel,
        ], 200);
    }

    public function getClient($id)
    {
        // Fetch the client data by ID
        $client = ClientModel::findOrFail($id);

        // Return the client data as a JSON response
        return response()->json($client);
    }

    public function show($id)
    {
        //User Detail
        $client = ClientModel::find($id);
        if (!$client) {
            return response()->json([
                'message' => 'No Client Found',
            ], 404);
        }
        return response()->json([
            'results' => $client,
        ], 200);
    }

    public function lists()
    {
        return ClientModel::all();
    }

    public function updateclient(Request $request, $id)
    {
        try {
            // Find Client
            $client = ClientModel::find($id);

            if (!$client) {
                return response()->json([
                    'message' => 'Client not found.',
                ], 404);
            }

            // Validate the request data
            // $request->validate([
            //     'partner_name' => 'string', // Make it nullable if not required
            //     'partner_logo' => 'nullable|image|mimes:jpeg,png,jpg',
            // ]);

            // Update client data only if 'partner_name' is provided
            if ($request->has('partner_name')) {
                $client->partner_name = $request->input('partner_name');
                $client->partner_logo = $request->input('partner_logo');
            }

            // Handle partner_logo if provided
            if ($request->hasFile('partner_logo')) {
                $partnerLogo = $request->file('partner_logo');
                // Log file information for debugging
                \info('Uploaded File Name: ' . $partnerLogo->getClientOriginalName());
                \info('Uploaded File Size: ' . $partnerLogo->getSize());
                \info('Uploaded File MIME Type: ' . $partnerLogo->getMimeType());
                $imageName = time() . '.' . $partnerLogo->getClientOriginalExtension();
                $partnerLogo->move(public_path('images'), $imageName);
                $client->partner_logo = $imageName;
            }

            $client->save();

            // Return JSON response
            return response()->json([
                'message' => 'Client successfully updated.',
                'data' => $client,
            ], 200);
        } catch (\Exception $e) {
            // Return JSON response for errors
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroydata(Request $request, $ids)
    {
        // dd($ids);
        try {
            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No IDs provided for deletion', 'code' => 400]);
            }

            // Convert the comma-separated string to an array of IDs
            $idArray = explode(',', $ids);

            // Perform the deletion
            $deletedRows = ClientModel::whereIn('id', $idArray)->delete();

            if ($deletedRows > 0) {
                return response()->json(['status' => 'success', 'message' => 'Clients deleted successfully', 'code' => 200]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No matching clients found for deletion', 'code' => 404]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => 500]);
        }
    }



    public function destroy(Request $request)
    {

        $multi_data = $request->deleteids_arr;

        $multi_data = $request->id;
        $multi_data2 = $request->deleteids_arr;
        if ($request->delete_type == 'multi') {
            $data = ClientModel::whereIn('id', $multi_data2)
                ->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Client Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else if ($request->delete_type == 'permanent') {
            $data = ClientModel::whereIn('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Client permanently Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        } else {
            // dd($request->id);
            //    $data = DB::table('roles')->where('id', $request->id)->delete();
            $data = ClientModel::where('id', $multi_data)->delete();
            if ($data) {
                return response()->json(['status' => "success", 'message' => 'Client Deleted successfully', 'code' => '200']);
            } else {
                return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
            }
        }
    }

    public function create()
    {

        return view('client.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'partner_name' => 'required',
            'partner_logo' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $imageName = $request->partner_logo->getClientOriginalName();
        $request->partner_logo->move(public_path('/images/client'), $imageName);

        $client = new ClientModel();
        $client->partner_name = $request->partner_name;
        $client->partner_logo = $imageName;
        $client->save();

        return response()->json([
            'success' => true,
            'message' => 'Client saved successfully',
        ]);
    }

    public function client_list()
    {
        //   $draw = $request->input('sEcho');
        //   $rowperpage = $request->input('iDisplayLength');
        //   // dd($rowperpage);
        //   $totalRecordwithFilter = $request->input('iTotalRecords');
        //   $data = ClientModel::all();
        //   $output = array(
        //     "sEcho" => intval($draw),
        //     "iTotalRecords" => $totalRecordwithFilter,
        //     "iTotalDisplayRecords" => $totalRecordwithFilter,
        //     "aaData" => $data,
        // );
        // dd($output);
        return view('client.index');
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

        $users = ClientModel::getclientdata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        $totalRecordwithFilter = ClientModel::getclientTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage);
        //  dd($totalRecordwithFilter);
        $render_array['ClientModel'] = $users;

        $data = array();
        if (count($render_array['ClientModel']) > 0) {

            foreach ($render_array['ClientModel'] as $key => $values) {

                $data[$key]['id'] = $values->idd;
                $data[$key]['partner_name'] = $values->partner_name;
                $data[$key]['partner_logo'] = (!empty($values->partner_logo) ? $values->partner_logo : '--');

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

    // public function destroy(Request $request)
    // {

    //     $multi_data = $request->deleteids_arr;

    //     $multi_data = $request->id;
    //     $multi_data2 = $request->deleteids_arr;
    //     if ($request->delete_type == 'multi') {
    //         $data = ClientModel::whereIn('id', $multi_data2)
    //             ->delete();
    //         if ($data) {
    //             return response()->json(['status' => "success", 'message' => 'Client Deleted successfully', 'code' => '200']);
    //         } else {
    //             return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
    //         }
    //     } else if ($request->delete_type == 'permanent') {
    //         $data = ClientModel::whereIn('id', $multi_data)->delete();
    //         if ($data) {
    //             return response()->json(['status' => "success", 'message' => 'Client permanently Deleted successfully', 'code' => '200']);
    //         } else {
    //             return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
    //         }
    //     } else {
    //         // dd($request->id);
    //         //    $data = DB::table('roles')->where('id', $request->id)->delete();
    //         $data = ClientModel::where('id', $multi_data)->delete();
    //         if ($data) {
    //             return response()->json(['status' => "success", 'message' => 'Client Deleted successfully', 'code' => '200']);
    //         } else {
    //             return response()->json(['status' => "error", 'message' => 'Something worng', 'code' => '400']);
    //         }
    //     }

    // }

    public function edit($id)
    {

        return view('client.edit', ['id' => $id]);
    }

    public function update(Request $request)
    {

        // dd($request);
        $client = ClientModel::find($request->id);
        // dd($request);
        // dd($testimonial);
        return response()->json($client);
    }

    public function update_save(Request $request)
    {

        $client = ClientModel::find($request->id);
        if ($client) {
            $client->partner_name = $request->partner_name;
            $partner_logo = $request->file('partner_logo');
            $partner_logo_old = $request->input("oldfile");

            if (isset($partner_logo)) {

                $orignalfilename = $partner_logo->getClientOriginalName();

                $client->partner_logo = $request->file('partner_logo' ?? null)->move(public_path('/images/client'), $orignalfilename);

            } else {

                $orignalfilename = $partner_logo_old;
            }

            $client->partner_logo = $orignalfilename;

            $client->update();
            return response()->json([
                'success' => true,
                'message' => 'Client Update successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Client Not Found',
            ]);
        }

    }

}
