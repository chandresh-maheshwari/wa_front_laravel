<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
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

        $homePage = HomePage::where('deleted_at', 0)->get();

        if ($homePage->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Home Page Data Fetch Successfully',
            'results' => $homePage,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

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
                'status' => true,
                'code' => '200',
                'message' => 'Home Page Added Successfully',
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
        $homePage = HomePage::where('id', $id)->where('deleted_at', 0)->first();

        if (!$homePage) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Home Page Data Fetch Successfully',
            'results' => $homePage,
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

        $data = HomePage::where('deleted_at', 0)->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Home Page Data Not Found',
            ], 404);
        }

        $data->home_section_img_url = url('/images/homePage/' . $data->home_section_img);

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Home Page data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function updateHomePage(Request $request, $id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $homePage = HomePage::find($id);

        if (!$homePage) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Home Page Data Not Found',

            ], 404);
        }

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

        $homePage->update();
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Home Page Data Updated Successfully',
        ], 200);
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

        $status = HomePage::find($id);

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
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        
        $deletePage = HomePage::find($id);

        if ($deletePage) {
            $deletePage->deleted_at = 1;
            if ($deletePage->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Home Page Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Home Page Found For Deletion',
        ], 404);
    }
}
