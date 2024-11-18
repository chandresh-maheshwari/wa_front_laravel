<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
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

        $post = Posts::where('deleted_at', 0)->get();

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
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'post_type' => 'required',
            'ordering' => 'required'
        ]);

        $posts1 = $request->image->getClientOriginalName();
        $request->image->move(public_path('/images/posts'), $posts1);

        $posts = new Posts();
        $posts->title = $request['title'];
        $posts->description = $request['description'];
        $posts->image = $posts1;
        $posts->post_type = $request['post_type'];
        if ($request['ordering'] == 0) {
            $posts->ordering = 1;
        } else {
            $posts->ordering = $request['ordering'];
        }
        $posts->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($posts->save() == true) {
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

        $post = Posts::where('id', $id)->where('deleted_at', 0)->first();
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

        $data = Posts::where('deleted_at', 0)->find($id);
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

    public function update(Request $request, $id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $posts = Posts::find($id);

        if (!$posts) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Post Data Not Found',

            ], 404);
        }

        if ($request->hasFile('image')) {
            $postImage = $request->image->getClientOriginalName();
            $request->image->move(public_path('/images/posts'), $postImage);
            $posts->image = $postImage;
        }

        $posts->title = $request->title;
        $posts->description = $request->description;
        $posts->post_type = $request->post_type;
        if ($request['ordering'] == 0) {
            $posts->ordering = 1;
        } else {
            $posts->ordering = $request['ordering'];
        }
        $posts->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        $posts->update();
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Post Data Updated Successfully',
        ], 200);
    }

    public function active($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $status = Posts::find($id);

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
        
        $deletePage = Posts::find($id);

        if ($deletePage) {
            $deletePage->deleted_at = 1;
            if ($deletePage->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Post Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Post Found For Deletion',
        ], 404);
    }
}

