<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        $homePage = HomePage::where('deleted_at', 0)->get();
        if (!$homePage) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Home Page Data Fetch Successfully',
            'results' => $homePage,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'home_section_img' => 'required|image|mimes:jpeg,png,jpg',
            'home_section_title' => 'required',
            'home_section_description' => 'required',
            'home_section_button_name' => 'required',
            'home_section_button_name_link' => 'required'

        ]);

        $homePageImage = $request->home_section_img->getClientOriginalName();
        $request->home_section_img->move(public_path('/images/homePage'), $homePageImage);


        $homePage = new HomePage();
        $homePage->home_section_img = $homePageImage;
        $homePage->home_section_title = $request['home_section_title'];
        $homePage->home_section_description = $request['home_section_description'];
        $homePage->home_section_button_name = $request['home_section_button_name'];
        $homePage->home_section_button_name_link = $request['home_section_button_name_link'];
        $homePage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($homePage->save() == true) {
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'Home Page Added Successfully',
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
        $homePage = HomePage::where('id', $id)->where('deleted_at', 0)->first();
        if (!$homePage) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Home Page Data Fetch Successfully',
            'results' => $homePage,
        ], 200);
    }

    public function edit($id)
    {
        $data = HomePage::where('deleted_at', 0)->findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }

        $data->home_section_img_url = url('/images/homePage/' . $data->home_section_img);

        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Home Page data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function updateHomePage(Request $request, $id)
    {
        $homePage = HomePage::find($request->id);

        // if ($request->hasFile('home_section_img')) {
        //     $file1 = $request->file('home_section_img');
        //     $originalName = $file1->getClientOriginalName();
        //     $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
        //     $file1->move(public_path('/images/homePage'), $imageName1);
        //     $homePage->home_section_img = url('/images/homePage/' . $imageName1);
        //     $homePage->home_section_img = url('/images/homePage/' . $originalName);
        // }

        if ($request->hasFile('home_section_img')) {
            $homePageImage = $request->home_section_img->getClientOriginalName();
            $request->home_section_img->move(public_path('/images/homePage'), $homePageImage);
            $homePage->home_section_img = $homePageImage;
        }
    
        $homePage->home_section_title = $request->home_section_title;
        $homePage->home_section_description = $request->home_section_description;
        $homePage->home_section_button_name = $request->home_section_button_name;
        $homePage->home_section_button_name_link = $request->home_section_button_name_link;
        $homePage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if (!$homePage) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Home Page Data Not Found',

            ], 404);
        }

        $homePage->update();
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Home Page Data Updated Successfully',
        ], 200);
    }

    public function active(Request $request, $id)
    { {
            $status = HomePage::find($id);
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
        $deletepage = HomePage::find($request->id);
        if ($deletepage) {
            $deletepage->deleted_at = 1;
            if ($deletepage->save()) {
                return response()->json([
                    'status' => 'Success',
                    'code' => '200',
                    'message' => 'Home Page Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => 'Error',
            'code' => '404',
            'message' => 'No Matching Home Page Found For Deletion',
        ], 404);
    }
}
