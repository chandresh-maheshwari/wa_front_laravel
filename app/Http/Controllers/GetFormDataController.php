<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class GetFormDataController extends Controller
{
    public function getFormData($postTitle)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        try {
            $post = DynamicPost::where('post_title', $postTitle)->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post not found',
                ], 404);
            }

            if ($post->deleted_at != 0) {
                return response()->json([
                    'status' => false,
                    'code' => '410',
                    'message' => 'This Record is deleted',
                ], 410);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'data' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred while retrieving the post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
