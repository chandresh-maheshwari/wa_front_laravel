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

    /** Function used for the listing create by ns */

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

    /** Function used for the store data in the database create by ns */

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
    /** Function used for the see particular id data create by ns */

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

    /** Function used for the edit data create by ns */

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

    /** Function used for the updata data create by ns */

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

        $this->validate($request, [
            'post_description.*.label' => 'sometimes|required|string',
            'post_description.*.type' => 'sometimes|required|string'
        ]);

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
}
