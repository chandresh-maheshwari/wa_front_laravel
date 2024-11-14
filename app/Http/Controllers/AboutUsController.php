<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutUsController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $aboutUs = AboutUs::where('deleted_at', 0)->get();

        if ($aboutUs->isEmpty()) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'About Us Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'About Us Data Fetch Successfully',
            'results' => $aboutUs,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $this->validate($request, [
            'title' => 'required',
            'name' => 'required',
            'description' => 'required',

        ]);

        $aboutUs = new AboutUs();
        $aboutUs->title = $request['title'];
        $aboutUs->name = $request['name'];
        $aboutUs->description = $request['description'];
        $aboutUs->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($aboutUs->save() == true) {
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'About Us Added Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
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
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $aboutUs = AboutUs::where('id', $id)->where('deleted_at', 0)->first();

        if (!$aboutUs) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'About Us Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'About Us Data Fetch Successfully',
            'results' => $aboutUs,
        ], 200);
    }

    public function edit()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = AboutUs::where('deleted_at', 0)->first();

        if (!$data) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'About Us Data Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'About Us Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $aboutUs = AboutUs::find($id);
        if (!$aboutUs) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'About Us Data Not Found',
            ], 404);
        }
        $aboutUs->title = $request->title;
        $aboutUs->name = $request->name;
        $aboutUs->description = $request->description;
        $aboutUs->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        $aboutUs->update();

        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'About Us Updated Successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        $deleteAboutUs = AboutUs::find($id);

        if ($deleteAboutUs) {
            $deleteAboutUs->deleted_at = 1;
            if ($deleteAboutUs->save()) {
                return response()->json([
                    'status' => 'Success',
                    'code' => '200',
                    'message' => 'About Us Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => 'Error',
            'code' => '404',
            'message' => 'No Matching About Us Found For Deletion',
        ], 404);
    }

    public function active($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        $status = AboutUs::find($id);
        if (!$status) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Record not found',
            ], 404);
        }

        $status->active = $status->active ? 0 : 1;
        $status->save();

        $message = $status->active ? 'Activated Successfully' : 'Deactivated Successfully';

        return response()->json([
            'status' => $status->active,
            'message' => $message,
        ]);
    }
}
