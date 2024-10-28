<?php

namespace App\Http\Controllers;

use App\Models\TopMenu;
use Illuminate\Http\Request;

class TopMenuController extends Controller
{

    public function index()
    {
        $menu = TopMenu::all();
        return response()->json([
            'results' => $menu,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'site_logo_img' => 'required|image|mimes:jpeg,png,jpg',
            'mts_logo_img' => 'required|image|mimes:jpeg,png,jpg',
            'site_logo_img_link' => 'required',
            'mts_logo_img_link' => 'required',
            'mts_group_text1' => 'required',
            'mts_group_text2' => 'required'

        ]);

        $siteImage = $request->site_logo_img->getClientOriginalName();
        $mtsImage = $request->site_logo_img->getClientOriginalName();
        $request->site_logo_img->move(public_path('/images/topmenu'), $siteImage);
        $request->mts_logo_img->move(public_path('/images/topmenu'), $mtsImage);

        $menu = new TopMenu();
        $menu->site_logo_img = $siteImage;
        $menu->mts_logo_img = $mtsImage;
        $menu->site_logo_img_link = $request['site_logo_img_link'];
        $menu->mts_logo_img_link = $request['mts_logo_img_link'];
        $menu->mts_group_text1 = $request['mts_group_text1'];
        $menu->mts_group_text2 = $request['mts_group_text2'];
        $menu->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($menu->save() == true) {
            return response()->json([
                'status' => 1,
                'message' => 'Top Menu added successfully',
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
        $menu = TopMenu::find($id);
        if (!$menu) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'results' => $menu,
        ], 200);
    }

    public function edit($id)
    {
        $data = TopMenu::findOrFail($id);
        return response()->json($data);
    }

    public function updateMenu(Request $request, $id)
    {
        $menu = TopMenu::find($request->id);

        $file1 = $request->file('site_logo_img');
        $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
        $file1->move(public_path('/images/topmenu'), $imageName1);

        $file2 = $request->file('mts_logo_img');
        $imageName2 = date('ymdhis') . rand(1000, 100000) . '.png';
        $file2->move(public_path('/images/topmenu'), $imageName2);

        // Get the base URL
        $baseUrl = url('/');

        $menu->site_logo_img = $baseUrl . '/images/topmenu/' . $imageName1;
        $menu->mts_logo_img = $baseUrl . '/images/topmenu/' . $imageName2;
        $menu->site_logo_img_link = $request->site_logo_img_link;
        $menu->mts_logo_img_link = $request->mts_logo_img_link;
        $menu->mts_group_text1 = $request->mts_group_text1;
        $menu->mts_group_text2 = $request->mts_group_text2;
        $menu->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
        $menu->update();

        return response()->json([
            'status' => 1,
            'message' => 'Top Menu updated successfully',
        ], 200);
    }

    public function destroy(Request $request, $id)
    {

        $deletemenu = TopMenu::find($request->id);
        if ($deletemenu) {
            $deletemenu->deleted_at = 1;
            if ($deletemenu->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Top Menu deleted successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No matching Top Menu found for deletion',
        ], 404);
    }
}
