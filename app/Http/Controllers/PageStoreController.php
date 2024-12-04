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

    /** 
     * List all page that are not deleted.
     * Ensures the user is authenticated before fetching the page. create by ns
     */
    public function getPageList($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $pageData = PageStore::where('id', $id)->where('deleted_at', 0)->get();

        if ($pageData->isEmpty()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'No Page Data Found',
                'results' => [],
            ], 200);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $pageData,
        ], 200);
    }
    /** Function used for the page value store in the database create by ns */

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

    /** 
     * Display a specific page by its postName.
     * Ensures the page is not deleted before displaying. create by ns
     */
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

        $pageData = pageStore::where('id', $id)->first();

        if (!$pageData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        if ($pageData->deleted_at != 0) {
            return response()->json([
                'status' => false,
                'code' => '410',
                'message' => 'This record is deleted',
            ], 410);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $pageData,
        ], 200);
    }

    /** 
     * Retrieve a post by its ID for editing.
     * Ensures the post is not deleted before fetching. create by ns
     */

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

        $data = pageStore::where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        if ($data->deleted_at != 0) {
            return response()->json([
                'status' => false,
                'code' => '410',
                'message' => 'This record is deleted',
            ], 410);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    /** 
     * Update a page
     * Ensures the page is not deleted before updating. create by ns
     */

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

        $pageData = PageStore::where('id', $id)->where('deleted_at', 0)->first();

        if (!$pageData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        $pages_name = $request->input('pages_name', null);
        $newData = $request->input('data', null);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $uploadFolder = 'uploads/dynamic_page_store';
            $file->move(public_path($uploadFolder), $originalName);
            $newData['image'] = URL::to($uploadFolder . '/' . $originalName);
        }

        if ($newData !== null) {
            if (is_string($newData)) {
                $newData = json_decode($newData, true);
            }

            if (!is_array($newData)) {
                return response()->json([
                    'status' => false,
                    'code' => '400',
                    'message' => 'Invalid data format',
                ], 400);
            }
        }
        try {
            $updateData = [];
            if ($pages_name !== null) {
                $updateData['pages_name'] = $pages_name;
            }
            if ($newData !== null) {
                $updateData['data'] = json_encode($newData);
            }

            $updated = PageStore::where('id', $id)->update($updateData);

            if ($updated) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Page Updated Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Update Page',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred while updating the page',
            ], 500);
        }
    }

    /** 
     * Soft delete a page by its id.
     * If the page is already deleted, it returns a message indicating so. create by ns
     */

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

        $pageData = pageStore::where('id', $id)->first();

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
                    'message' => 'Page Data Deleted Successfully',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Failed To Delete Page',
            ], 500);
        }
    }

    /** 
     * Toggle the active status of a page by its id.
     * If the page is active, it will be deactivated. create by ns
     */
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

        $data = pageStore::where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record not found',
            ], 404);
        }

        $data->status = $data->status ? 0 : 1;
        $data->save();

        $message = $data->status ? 'Activated Successfully' : 'Deactivated Successfully';

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => $message,
            'data' => $data->status
        ]);
    }
}
