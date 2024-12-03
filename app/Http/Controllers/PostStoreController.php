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
    public function getList($postName)
    {

        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $postData = PostStore::where('post_name', $postName)->where('deleted_at', 0)->get();

        if ($postData->isEmpty()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'No Post Data Found',
                'results' => [],
            ], 200);
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

    /** 
     * Display a specific post by its postName.
     * Ensures the post is not deleted before displaying. create by ns
     */
    public function show($postName)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $post = postStore::where('post_name', $postName)->first();

        if (!$post) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',
            ], 404);
        }

        if ($post->deleted_at != 0) {
            return response()->json([
                'status' => false,
                'code' => '410',
                'message' => 'This record is deleted',
            ], 410);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Post Data Fetch Successfully',
            'results' => $post,
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

        $data = postStore::where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',
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
            'message' => 'Post Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    /** 
     * Update a post's title and description by its postName.
     * Ensures the post is not deleted before updating. create by ns
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

        $post = PostStore::where('id', $id)->where('deleted_at', 0)->first();

        if (!$post) {
            return response()->json([
                'status' => false, 
                'code' => '404',
                'message' => 'Post Data Not Found',
            ], 404);
        }

        $post_name = $request->input('post_name', null);

        if ($post_name !== null) {
            $post->post_name = $post_name;
        }

        $newData = $request->input('data', null);

        if ($newData !== null) {
            if (is_string($newData)) {
                $newData = [$newData];
            }

            $post->data = json_encode($newData);
        }

        if ($post->save()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Updated Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Failed to update post',
            ], 500);
        }
    }


    /** 
     * Soft delete a post by its title.
     * If the post is already deleted, it returns a message indicating so. create by ns
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

        $post = postStore::where('id', $id)->first();

        if ($post) {
            if ($post->deleted_at == 1) {
                return response()->json([
                    'status' => false,
                    'code' => '400',
                    'message' => 'Record already deleted',
                ], 400);
            }

            $post->deleted_at = 1;
            if ($post->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Post Data Deleted Successfully',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Failed to delete post',
            ], 500);
        }
    }

    /** 
     * Toggle the active status of a post by its title.
     * If the post is active, it will be deactivated. create by ns
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

        $data = postStore::where('id', $id)->first();

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
