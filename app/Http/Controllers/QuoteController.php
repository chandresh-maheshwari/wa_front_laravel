<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{

    public function index()
    {
        $quoteSection = Quote::where('deleted_at', 0)->get();

        // return json format
        return response()->json([
            'results' => $quoteSection,
        ], 200);
    }

    public function store(Request $request)
    {
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
                'status' => 1,
                'message' => 'Quote Section added successfully',
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
        $quoteSection = Quote::find($id);
        if (!$quoteSection) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'results' => $quoteSection,
        ], 200);
    }

    public function edit($id)
    {
        $data = Quote::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Quote section data fetch successfully',
            'results' => $data,
        ], 200);
    }

    public function updateQuoteSection(Request $request ,$id)
    {
        $quoteSection = Quote::find($request->id);
        $quoteSection->title = $request->title;
        $quoteSection->designation = $request->designation;
        $quoteSection->company_name = $request->company_name;
        $quoteSection->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
        $quoteSection->update();

        return response()->json([
            'status' => 1,
            'message' => 'Quote Section updated successfully',
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $deletequote = Quote::find($request->id);
        // dd($deletemenu);
        if ($deletequote) {
            $deletequote->deleted_at = 1;
            if ($deletequote->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Quote Section deleted successfully',

                ],200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No matching Quote Section found for deletion',
        ],404);
    }
}
