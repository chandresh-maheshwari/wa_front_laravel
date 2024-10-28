<?php

namespace App\Http\Controllers;

use App\Models\NavBar;
use Illuminate\Http\Request;

class NavBarController extends Controller
{

    public function index()
    {
        $navbar = NavBar::all();

        return response()->json([
            'results' => $navbar,
        ], 200);
    }
    public function store(Request $request)
    {
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
                'status' => 1,
                'message' => 'Navbar added successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    public function show($id)
    {
        $navbar = NavBar::find($id);
        if (!$navbar) {
            return response()->json([
                'status' => 0,
                'message' => 'Navbar Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'results' => $navbar,
        ], 200);
    }

    public function edit($id)
    {
        $data = NavBar::findOrFail($id);
        return response()->json($data);
    }

    public function updateNavbar(Request $request, $id)
    {
        $navbar = NavBar::find($request->id);

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
            'status' => 1,
            'message' => 'Nav Bar updated successfully',
        ], 200);
    }
    public function destroy(Request $request, $id)
    {
        $deletenavbar = NavBar::find($request->id);
        if ($deletenavbar) {
            $deletenavbar->deleted_at = 1;
            if ($deletenavbar->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Nav Bar deleted successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No matching Nav Bar found for deletion',
        ], 404);
    }
}
