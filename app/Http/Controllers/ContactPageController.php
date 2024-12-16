<?php

namespace App\Http\Controllers;

use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactPageController extends Controller
{

    public function index()
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $contactData = ContactPage::where('deleted_at', 0)->orderBy('id', 'desc')->get();

        if ($contactData->isEmpty()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'No Data Found',
                'results' => [],
            ], 200);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Contact Data Fetch Successfully',
            'results' => $contactData,
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
            'name' => 'required',
            'email' => 'required',
            'contact_number' => 'required|digits_between:10,11',
            'description' => 'required',
        ], [
            'contact_number.digits_between' => 'The contact number must be a numeric value between 10 and 11 digits.',
            'contact_number.required' => 'The contact number field is required.',
        ]);

        $contactPage = new ContactPage();

        $contactPage->name = $request->name;
        $contactPage->email = $request->email;
        $contactPage->contact_number = $request->contact_number;
        $contactPage->description = $request->description;

        $contactPage->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($contactPage->save()) {
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


    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $pageData = ContactPage::where('id', $id)->first();

            if ($pageData) {
                if ($pageData->deleted_at == 1) {
                    return response()->json([
                        'status' => false,
                        'code' => '400',
                        'message' => 'Record already deleted',
                    ], 400);
                }

                $pageData->deleted_at = 1;
                if ($pageData->save()) {
                    return response()->json([
                        'status' => true,
                        'code' => '200',
                        'message' => 'Contact Page Data Deleted Successfully',
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Delete Page',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
