<?php

namespace App\Http\Controllers;

use App\Models\ImproveEnvirmentalProtection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImproveEnvirmentalPortectionController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $improveEnvirmentalProtection= ImproveEnvirmentalProtection::where('deleted_at', 0)->get();

        if ($improveEnvirmentalProtection->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Improve Envirmental Protection Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Improve Envirmental Protection Data Fetch Successfully',
            'results' => $improveEnvirmentalProtection,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $this->validate($request, [
            'protections_description' => 'required',
            'button_name' => 'required',
            'button_link' => 'required'
        ]);
        $improveEnvirmentalProtection = new ImproveEnvirmentalProtection();
        $improveEnvirmentalProtection->protections_description = $request['protections_description'];
        $improveEnvirmentalProtection->button_name = $request['button_name'];
        $improveEnvirmentalProtection->button_link = $request['button_link'];
        $improveEnvirmentalProtection->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($improveEnvirmentalProtection->save() == true) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Improve Envirmental Protection Added Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    public function show($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        $improveEnvirmentalProtection = ImproveEnvirmentalProtection::where('id', $id)->where('deleted_at', 0)->first();

        if (!$improveEnvirmentalProtection) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Improve Envirmental Protection Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Improve Envirmental Protection Data Fetch Successfully',
            'results' => $improveEnvirmentalProtection,
        ], 200);
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = ImproveEnvirmentalProtection::where('deleted_at', 0)->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Improve Envirmental Protection Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Improve Envirmental Protection Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $improveEnvirmentalProtection = ImproveEnvirmentalProtection::find($id);

        if (!$improveEnvirmentalProtection) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Improve Envirmental Protection Data Not Found',

            ], 404);
        }
        $improveEnvirmentalProtection->protections_description = $request->protections_description;
        $improveEnvirmentalProtection->button_name = $request->button_name;
        $improveEnvirmentalProtection->button_link = $request->button_link;
        $improveEnvirmentalProtection->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;


        $improveEnvirmentalProtection->update();
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Improve Envirmental Protection Updated Successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $deleteProtection = ImproveEnvirmentalProtection::find($id);

        if ($deleteProtection) {
            $deleteProtection->deleted_at = 1;
            if ($deleteProtection->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Improve Envirmental Protection Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Improve Envirmental Protection Found For Deletion',
        ], 404);
    }

    public function active($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $status = ImproveEnvirmentalProtection::find($id);

        if (!$status) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record not found'
            ], 404);
        }

        $status->active = $status->active ? 0 : 1;
        $status->save();

        $message = $status->active ? 'Activated Successfully' : 'Deactivated Successfully';

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => $message,
            'data' => $status->active
        ]);
    }
}
