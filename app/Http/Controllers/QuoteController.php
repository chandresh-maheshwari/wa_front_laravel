<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
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

        $quoteSection = Quote::where('deleted_at', 0)->get();

        if ($quoteSection->isEmpty()) {
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
            'results' => $quoteSection,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $this->validate($request, [
            'title' => 'required',
            'designation' => 'required',
            'company_name' => 'required'

        ]);

        $quoteSection = new Quote();
        $quoteSection->title = $request['title'];
        $quoteSection->designation = $request['designation'];
        $quoteSection->company_name = $request['company_name'];
        $quoteSection->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($quoteSection->save() == true) {
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'Quote Section Added Successfully',
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
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $quoteSection = Quote::where('id', $id)->where('deleted_at', 0)->first();

        if (!$quoteSection) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Quote Section Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Quote Section Data Fetch Successfully',
            'results' => $quoteSection,
        ], 200);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = Quote::where('deleted_at', 0)->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Quote Section Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Quote section Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function updateQuoteSection(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $quoteSection = Quote::find($id);

        if (!$quoteSection) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Quote Section Data Not Found',

            ], 404);
        }
        $quoteSection->title = $request->title;
        $quoteSection->designation = $request->designation;
        $quoteSection->company_name = $request->company_name;
        $quoteSection->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;


        $quoteSection->update();
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Quote Section Data Updated Successfully',
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

        $deletequote = Quote::find($id);

        if ($deletequote) {
            $deletequote->deleted_at = 1;
            if ($deletequote->save()) {
                return response()->json([
                    'status' => 'Success',
                    'code' => '200',
                    'message' => 'Quote Section Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => 'Error',
            'code' => '404',
            'message' => 'No Matching Quote Section Found For Deletion',
        ], 404);
    }

}
