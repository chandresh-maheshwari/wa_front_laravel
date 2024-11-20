<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DynamicPostController extends Controller
{
    protected $request;
    protected $dynamicPost1;
    function __construct(Request $request, DynamicPost $dynamicPost)
    {
        $this->request = $request;
        $this->dynamicPost1 = $dynamicPost;
    }

    /** 
     * List all posts that are not deleted.
     * Ensures the user is authenticated before fetching the posts. create by ns
     */

    public function listPosts()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $post = DynamicPost::where('deleted_at', 0)->get();

        if ($post->isEmpty()) {
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
            'results' => $post,
        ], 200);
    }

    /** 
     * Store a new post in the database.
     * Validates the request data before saving. create by ns
     */

    public function addPost(Request $request)
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
            'post_title' => 'required|string|max:255',
            'post_description' => 'required',
            'post_description.*.label' => 'required|string',
            'post_description.*.type' => 'required|string'
        ]);

        $postData = $request['post_description'];

        $saveData = $this->dynamicPost1->savePost([
            'post_title' => $request['post_title'],
            'post_description' => $postData
        ]);

        if (isset($saveData) && $saveData !== false) {
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
     * Display a specific post by its ID.
     * Ensures the post is not deleted before displaying. create by ns
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

        $post = DynamicPost::where('id', $id)->where('deleted_at', 0)->first();
        if (!$post) {
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

        $data = DynamicPost::where('deleted_at', 0)->find($id);
        if (!$data) {
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
            'results' => $data,
        ], 200);
    }

    /** 
     * Update a post's title and description by its ID.
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

        $post = DynamicPost::where('id', $id)->where('deleted_at', 0)->first();
        if (!$post) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',
            ], 404);
        }

        $post->post_title = $request['post_title'];

        if ($request->has('post_description')) {
            $post->post_description = $request['post_description'];
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

    public function destroy($postTitle)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $post = DynamicPost::where('post_title', $postTitle)->first();

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
    public function active($postTitle)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = DynamicPost::where('post_title', $postTitle)->first();

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
