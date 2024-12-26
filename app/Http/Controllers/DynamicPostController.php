<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = DynamicPost::where('deleted_at', 0)->orderBy('id', 'desc')->get();

            if ($post->isEmpty()) {
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
                'message' => 'Dynamic Post Data Fetch Successfully',
                'results' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Store a new post in the database.
     * Validates the request data before saving. create by ns
     */

    public function addPost(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $this->validate($request, [
                'post_title' => 'required|string|max:255',
                'post_description' => 'required',
                'post_description.*.label' => 'required|string',
                'post_description.*.type' => 'required|string',
                'post_type' => 'required|string',
                'ordering' => 'sometimes|integer|min:1',
            ]);

            $postData = $request['post_description'];
            $postData1 = $request['post_type'];

            $ordering = $request['ordering'] ?? 1;
            if ($ordering == 0) {
                $ordering = 1;
            }

            $saveData = $this->dynamicPost1->savePost([
                'post_title' => $request['post_title'],
                'post_description' => $postData,
                'post_type' => $postData1,
                'ordering' => $ordering
            ]);

            if (isset($saveData) && $saveData !== false) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Dynamic Post Data Added Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Something Went Wrong'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Display a specific post by its ID.
     * Ensures the post is not deleted before displaying. create by ns
     */

    public function show($id)
    {
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = DynamicPost::where('id', $id)->where('deleted_at', 0)->first();
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Dynamic Post Data Not Found',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Dynamic Post Data Fetch Successfully',
                'results' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Retrieve a post by its ID for editing.
     * Ensures the post is not deleted before fetching. create by ns
     */

    public function edit($id)
    {
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $data = DynamicPost::where('deleted_at', 0)->find($id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Dynamic Post Data Not Found',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Dynamic Post Data Fetch Successfully',
                'results' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Update a post's title and description by its ID.
     * Ensures the post is not deleted before updating. create by ns
     */

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = DynamicPost::where('id', $id)->where('deleted_at', 0)->first();
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Dynamic Post Data Not Found',
                ], 404);
            }

            $post->post_title = $request['post_title'];

            if ($request->has('post_description')) {
                $post->post_description = $request['post_description'];
            }

            if ($request->has('post_type')) {
                $post->post_type = $request['post_type'];
            }

            if ($request->has('ordering')) {
                $post->ordering = $request['ordering'];
            }
            if ($post->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Dynamic Post Data Updated Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Update Dynamic Post',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Soft delete a post by its title.
     * If the post is already deleted, it returns a message indicating so. create by ns
     */

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $ids = explode(',', $id);
            $ids = array_filter($ids);

            if (count($ids) > 1) {
                $deletedCount = DynamicPost::whereIn('id', $ids)->update(['deleted_at' => 1]);

                if ($deletedCount > 0) {
                    return response()->json([
                        'status' => true,
                        'code' => '200',
                        'message' => 'Mutli Dynamic Post Data Deleted Successfully',
                        'deleted_count' => $deletedCount,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '404',
                        'message' => 'No Dynamic Post Found To Delete',
                    ], 404);
                }
            } else {
                $post = DynamicPost::where('id', $ids[0])->first();

                if ($post) {
                    if ($post->deleted_at == 1) {
                        return response()->json([
                            'status' => false,
                            'code' => '400',
                            'message' => 'Record Already Deleted',
                        ], 400);
                    }

                    $post->deleted_at = 1;
                    if ($post->save()) {
                        return response()->json([
                            'status' => true,
                            'code' => '200',
                            'message' => 'Dynamic Post Data Deleted Successfully',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '500',
                        'message' => 'Failed To Delete Dynamic Post',
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /** 
     * Toggle the active status of a post by its title.
     * If the post is active, it will be deactivated. create by ns
     */
    public function active(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $idsArray = explode(',', $id);

            $validatedData = Validator::make(
                ['ids' => $idsArray],
                ['ids' => 'required|array|min:1'],
                ['ids.*' => 'integer|exists:dynamic_posts,id']
            );

            if ($validatedData->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validatedData->errors(),
                ], 422);
            }

            $posts = DynamicPost::whereIn('id', $idsArray)->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Records Found',
                ], 404);
            }
            $newStatus = $request->input('status');

            if ($newStatus !== null && in_array($newStatus, [0, 1])) {
                $posts->each(function ($post) use ($newStatus) {
                    $post->status = $newStatus;
                    $post->save();
                });
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '422',
                    'message' => 'Invalid Status Value',
                ], 422);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Dynamic Post Data Updated Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
