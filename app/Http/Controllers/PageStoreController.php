<?php

namespace App\Http\Controllers;

use App\Models\DynamicPage;
use App\Models\PageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class PageStoreController extends Controller
{
    /** Function used for the post value store in the database create by ns */

    public function pageStore(Request $request, $pagesName)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $pageData = DynamicPage::where('page_name', $pagesName)->first();

        if (!$pageData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        $pageDataArray = is_array($pageData->page_data) ? $pageData->page_data : json_decode($pageData->page_data, true);

        if (!is_array($pageDataArray)) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Invalid page data format',
            ], 500);
        }

        $requiredFields = [];
        $labelMap = [];
        foreach ($pageDataArray as $field) {
            $normalizedLabel = str_replace(' ', '_', $field['label']);
            $labelMap[$normalizedLabel] = $field['label'];
            if ($field['type'] === 'file') {
                $requiredFields[$normalizedLabel] = 'required|file|mimes:jpeg,png,gif,svg|max:2048';
            } else {
                $requiredFields[$normalizedLabel] = 'required|string';
            }
        }

        $requestData = $request->all();
        $formattedRequestData = [];
        foreach ($requestData as $key => $value) {
            $formattedRequestData[str_replace(' ', '_', $key)] = $value;
        }
        $validateRequest = Validator::make($formattedRequestData, $requiredFields);

        if ($validateRequest->fails()) {
            Log::error('Validation failed', $validateRequest->errors()->toArray());
            return response()->json([
                'status' => false,
                'code' => '404',
                'errors' => $validateRequest->errors()
            ], 404);
        }

        $data = [];
        foreach ($requiredFields as $normalizedLabel => $rules) {
            $originalLabel = $labelMap[$normalizedLabel];
            $data[$originalLabel] = $formattedRequestData[$normalizedLabel] ?? null;
        }

        foreach ($pageDataArray as $field) {
            $normalizedLabel = str_replace(' ', '_', $field['label']);
            if ($field['type'] === 'file' && $request->hasFile($normalizedLabel)) {
                $file = $request->file($normalizedLabel);
                $originalName = $file->getClientOriginalName();
                $uploadFolder = 'uploads/dynamic_page_store';
                $file->move(public_path($uploadFolder), $originalName);
                $data[$field['label']] = URL::to($uploadFolder . '/' . $originalName);
            }
        }

        $pageData = PageStore::create([
            'pages_name' => $pagesName,
            'data' => $data,
        ]);

        if ($pageData) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Page Added Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Something went wrong'
            ], 404);
        }
    }
}
