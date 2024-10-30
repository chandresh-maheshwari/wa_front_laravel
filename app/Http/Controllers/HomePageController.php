<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        $homePage = HomePage::where('deleted_at', 0)->get(); 

        // return json format
        return response()->json([
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
                'status' => 1,
                'message' => 'Home Page added successfully',
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
        $homePage = HomePage::find($id);
        if (!$homePage) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'results' => $homePage,
        ], 200);
    }

    public function edit($id)
    {
        $data = HomePage::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $homePage = HomePage::find($request->id);
      
        if ($request->hasFile('home_section_img')) {
            $file1 = $request->file('home_section_img');
            $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
            $file1->move(public_path('/images/homePage'), $imageName1);
            $homePage->home_section_img = url('/images/homePage/' . $imageName1);
        }
        $homePage->home_section_title = $request->home_section_title;
        $homePage->home_section_description = $request->home_section_description;
        $homePage->home_section_button_name = $request->home_section_button_name;
        $homePage->home_section_button_name_link = $request->home_section_button_name_link;
        $homePage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
        $homePage->update();

        return response()->json([
            'status' => 1,
            'message' => 'Home Page updated successfully',
        ], 200);
    }

    public function destroy(Request $request, $id)
    {

        $deletepage = HomePage::find($request->id);
        if ($deletepage) {
            $deletepage->deleted_at = 1;
            if ($deletepage->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Home Page deleted successfully',

                ],200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No matching Home Page found for deletion',
        ],404);
    }
}
