<?php

namespace App\Http\Controllers;

use App\Models\NavBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavBarController extends Controller
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

        $navbar = NavBar::where('deleted_at', 0)->get();

        if ($navbar->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Nav Bar Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Nav Bar Data Fetch Successfully',
            'results' => $navbar,
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
            'nav_menu_name' => 'required',
            'nav_menu_link' => 'required',
            'menu_ordering' => 'required',

        ]);

        $navbar = new NavBar();
        $navbar->nav_menu_name = $request['nav_menu_name'];
        $navbar->nav_menu_link = $request['nav_menu_link'];
        if ($request['menu_ordering'] == 0) {
            $navbar->menu_ordering = 1;
        } else {
            $navbar->menu_ordering = $request['menu_ordering'];
        }
        $navbar->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($navbar->save() == true) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Nav Bar Added Successfully',
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

        $navbar = NavBar::where('id', $id)->where('deleted_at', 0)->first();
        if (!$navbar) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Nav Bar Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Nav Bar Data Fetch Successfully',
            'results' => $navbar,
        ], 200);
    }

    public function edit($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = NavBar::where('deleted_at', 0)->find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Nav Bar Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Nav Bar Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function updateNavbar(Request $request, $id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $navbar = NavBar::find($id);

        if (!$navbar) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Nav Bar Data Not Found',
            ], 404);
        }
        $navbar->nav_menu_name = $request->nav_menu_name;
        $navbar->nav_menu_link = $request->nav_menu_link;
        if ($request['menu_ordering'] == 0) {
            $navbar->menu_ordering = 1;
        } else {
            $navbar->menu_ordering = $request['menu_ordering'];
        }
        $navbar->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        $navbar->update();
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Nav Bar Updated Successfully',
        ], 200);
    }

    /** Function used for the if status active or deactive by ns */

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

        $status = NavBar::find($id);
        if (!$status) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record not found',
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

    public function destroy($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $deletenavbar = NavBar::find($id);
        if ($deletenavbar) {
            $deletenavbar->deleted_at = 1;
            if ($deletenavbar->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Nav Bar Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Nav Bar Found For Deletion',
        ], 404);
    }
}
