<?php

namespace App\Http\Controllers;

use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactPageController extends Controller
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

        $contactPage = ContactPage::where('deleted_at', 0)->get();

        if ($contactPage->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Contact Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Contact Page Data Fetch Successfully',
            'results' => $contactPage,
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
            'title' => 'required',
            'tagline' => 'required',
            'name' => 'required',
            'email' => 'required',
            'description' => 'required',
            'button_name' => 'required',
            'button_name_link' => 'required'

        ]);

        $contactPage = new ContactPage();
        $contactPage->title = $request['title'];
        $contactPage->tagline = $request['tagline'];
        $contactPage->name = $request['name'];
        $contactPage->email = $request['email'];
        $contactPage->description = $request['description'];
        $contactPage->button_name = $request['button_name'];
        $contactPage->button_name_link = $request['button_name_link'];
        $contactPage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($contactPage->save() == true) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Contact Page Added Successfully',
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

        $contactPage = ContactPage::where('id', $id)->where('deleted_at', 0)->first();

        if (!$contactPage) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Contact Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Contact Page Data Fetch Successfully',
            'results' => $contactPage,
        ], 200);
    }

    public function edit()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = ContactPage::where('deleted_at', 0)->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Contact Page Data Not Found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Contact Page Data Fetch Successfully',
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

        $contactPage = ContactPage::find($id);
        if (!$contactPage) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Contact Page Data Not Found',
            ], 404);
        }
        $contactPage->title = $request->title;
        $contactPage->tagline = $request->tagline;
        $contactPage->name = $request->name;
        $contactPage->email = $request->email;
        $contactPage->description = $request->description;
        $contactPage->button_name = $request->button_name;
        $contactPage->button_name_link = $request->button_name_link;
        $contactPage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        $contactPage->update();

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Contact Page Updated Successfully',
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
        $deleteContactPage = ContactPage::find($id);

        if ($deleteContactPage) {
            $deleteContactPage->deleted_at = 1;
            if ($deleteContactPage->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Contact Page Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Contact Page Found For Deletion',
        ], 404);
    }

    public function active($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        $status = ContactPage::find($id);
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
}
