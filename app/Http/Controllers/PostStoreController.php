<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\PostStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class PostStoreController extends Controller
{

    /** 
     * List all posts that are not deleted.
     * Ensures the user is authenticated before fetching the posts. create by ns
     */
    public function getList()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $postData = PostStore::where('deleted_at', 0)->get();

        if ($postData->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Post Data Fetch Successfully',
            'results' => $postData,
        ], 200);
    }

    /** Function used for the post value store in the database create by ns */

    public function postStore(Request $request, $postTitle)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $postData = DynamicPost::where('post_title', $postTitle)->first();

        if (!$postData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',
            ], 404);
        }

        $requiredFields = [];
        $labelMap = [];
        foreach ($postData->post_description as $field) {
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

        foreach ($postData->post_description as $field) {
            $normalizedLabel = str_replace(' ', '_', $field['label']);
            if ($field['type'] === 'file' && $request->hasFile($normalizedLabel)) {
                $file = $request->file($normalizedLabel);
                $originalName = $file->getClientOriginalName();
                $uploadFolder = 'uploads/dynamic_post_store';
                $file->move(public_path($uploadFolder), $originalName);
                $data[$field['label']] = URL::to($uploadFolder . '/' . $originalName);
            }
        }

        $postData = PostStore::create([
            'post_name' => $postTitle,
            'data' => $data,
        ]);

        if ($postData) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Added Successfully',
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
