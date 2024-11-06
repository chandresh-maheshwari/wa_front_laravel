<?php

namespace App\Http\Controllers;

use App\Models\TopMenu;
use Illuminate\Http\Request;

class TopMenuController extends Controller
{
    public function index()
    {
        $menu = TopMenu::where('deleted_at', 0)->get();
        if (!$menu) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Top Menu Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Top Menu Data Fetch Successfully',
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
                'status' => 'Success',
                'code' => '200',
                'message' => 'Top Menu Added Successfully',
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
        $menu = TopMenu::where('id', $id)->where('deleted_at', 0)->first();
        if (!$menu) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Top Menu Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Top Menu Data Fetch Successfully',
            'results' => $menu,
        ], 200);
    }

    public function edit($id)
    {
        $data = TopMenu::where('deleted_at', 0)->findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Top Menu Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Top Menu Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function updateMenu(Request $request, $id)
    {
        $menu = TopMenu::find($request->id);

        if ($request->hasFile('site_logo_img')) {
            $file1 = $request->file('site_logo_img');
            $originalName = $file1->getClientOriginalName();
            $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
            $file1->move(public_path('/images/topmenu'), $imageName1);
            $menu->site_logo_img = url('/images/topmenu/' . $imageName1);
            $menu->site_logo_img = url('/images/topmenu/' . $originalName);
        }

        if ($request->hasFile('mts_logo_img')) {
            $file2 = $request->file('mts_logo_img');
            $originalName1 = $file1->getClientOriginalName();
            $imageName2 = date('ymdhis') . rand(1000, 100000) . '.png';
            $file2->move(public_path('/images/topmenu'), $imageName2);
            $menu->mts_logo_img = url('/images/topmenu/' . $imageName2);
            $menu->mts_logo_img = url('/images/topmenu/' . $originalName1);
        }
        $menu->site_logo_img_link = $request->site_logo_img_link;
        $menu->mts_logo_img_link = $request->mts_logo_img_link;
        $menu->mts_group_text1 = $request->mts_group_text1;
        $menu->mts_group_text2 = $request->mts_group_text2;
        $menu->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
        if (!$menu) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Top Menu Data Not Found',
            ], 404);
        }
        
        $menu->update();
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Top Menu Updated Successfully',
        ], 200);
        
    }

    public function active(Request $request, $id)
    { {
            $status = TopMenu::find($id);
            if (!$status) {
                return response()->json(['error' => 'Record not found'], 404);
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

    public function destroy(Request $request, $id)
    {
        $deletemenu = TopMenu::find($request->id);
        if ($deletemenu) {
            $deletemenu->deleted_at = 1;
            if ($deletemenu->save()) {
                return response()->json([
                    'status' => 'Success',
                    'code' => '200',
                    'message' => 'Top Menu Data Deleted Successfully',
                ], 200);
            }
        }
        return response()->json([
            'status' => 'Error',
            'code' => '404',
            'message' => 'No Matching Top Menu Found For Deletion',
        ], 404);
    }
}
