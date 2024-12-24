<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\PostStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Exception;

class PostStoreController extends Controller
{

    /** 
     * List all posts that are not deleted.
     * Ensures the user is authenticated before fetching the posts. create by ns
     */
    public function getList($postName)
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

            $postData = PostStore::where('post_name', $postName)->where('deleted_at', 0)->get();

            if ($postData->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'No Post Store Data Found',
                    'results' => [],
                ], 200);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $postData,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching post list', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /** Function used for the post value store in the database create by ns */

    public function postStore(Request $request, $postTitle)
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

            $postData = DynamicPost::where('post_title', $postTitle)->first();

            if (!$postData) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            $requestData = $request->all();
            $transformedRequest = [];
            foreach ($requestData as $key => $value) {
                $originalKey = str_replace('_', ' ', $key);
                $transformedRequest[$originalKey] = $value;
            }

            $requiredFields = [];
            $labelMap = [];
            foreach ($postData->post_description as $field) {
                $originalLabel = $field['label'];
                $slugLabel = $this->convertToSlug($originalLabel);
                $labelMap[$originalLabel] = $slugLabel;

                if ($field['type'] === 'file') {
                    $requiredFields[$originalLabel] = 'nullable|file|mimes:jpeg,png,gif,svg|max:2048';
                } else {
                    $requiredFields[$originalLabel] = 'nullable|string';
                }
            }

            $validateRequest = Validator::make($transformedRequest, $requiredFields);

            if ($validateRequest->fails()) {
                Log::error('Validation failed', $validateRequest->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'errors' => $validateRequest->errors()
                ], 404);
            }

            $data = [];

            foreach ($postData->post_description as $field) {
                $originalLabel = $field['label'];
                $value = $transformedRequest[$originalLabel] ?? $request->input(str_replace(' ', '_', $originalLabel));

                if ($value === null) {
                    continue;
                }

                if ($field['type'] === 'file' && $request->hasFile(str_replace(' ', '_', $originalLabel))) {
                    $file = $request->file(str_replace(' ', '_', $originalLabel));
                    $originalName = $file->getClientOriginalName();
                    $uploadFolder = 'uploads/dynamic_post_store';
                    $file->move(public_path($uploadFolder), $originalName);
                    $value = URL::to($uploadFolder . '/' . $originalName);
                }

                $data[$originalLabel] = $value;
                $data['field_slug_' . $this->convertToSlug($originalLabel)] = $labelMap[$originalLabel];
            }

            $postData = PostStore::create([
                'post_name' => $postTitle,
                'post_id' => $postData->id,
                'data' => $data,
            ]);

            if ($postData) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Post Store Data Added Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Something Went Wrong'
                ], 404);
            }
        } catch (Exception $e) {
            Log::error('Error storing post', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /** 
     * Display a specific post by its postName.
     * Ensures the post is not deleted before displaying. create by ns
     */
    public function show($postName)
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

            $post = PostStore::where('post_name', $postName)->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            if ($post->deleted_at != 0) {
                return response()->json([
                    'status' => false,
                    'code' => '410',
                    'message' => 'This Record Is Deleted',
                ], 410);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $post,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error showing post', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
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

            $data = PostStore::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            if ($data->deleted_at != 0) {
                return response()->json([
                    'status' => false,
                    'code' => '410',
                    'message' => 'This Record Is Deleted',
                ], 410);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $data,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error editing post', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /** 
     * Update a post's title and description by its postName.
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

            $post = PostStore::where('id', $id)->where('deleted_at', 0)->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            $post_name = $request->input('post_name', null);
            if ($post_name !== null) {
                $post->post_name = $post_name;
            }

            $data = $post->data ?? [];

            foreach ($request->all() as $key => $value) {
                $normalizedKey = str_replace('_', ' ', $key);

                if ($key === 'post_name') {
                    continue;
                }

                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $originalName = $file->getClientOriginalName();
                    $uploadFolder = 'uploads/dynamic_post_store';
                    $file->move(public_path($uploadFolder), $originalName);
                    $data[$normalizedKey] = URL::to($uploadFolder . '/' . $originalName);
                } else {
                    $data[$normalizedKey] = $value;
                }

                $slugKey = 'field_slug_' . $this->convertToSlug($normalizedKey);
                $data[$slugKey] = $this->convertToSlug($normalizedKey);
            }

            $post->data = $data;
            if ($post->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Post Store Data Updated Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Update Post Store ',
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Error updating post', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
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
                $deletedCount = PostStore::whereIn('id', $ids)->update(['deleted_at' => 1]);

                if ($deletedCount > 0) {
                    return response()->json([
                        'status' => true,
                        'code' => '200',
                        'message' => 'Multi Post Store Data Deleted Successfully',
                        'deleted_count' => $deletedCount,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '404',
                        'message' => 'No Post Store Found To Delete',
                    ], 404);
                }
            } else {
                $post = PostStore::where('id', $ids[0])->first();

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
                            'message' => 'Post Store Data Deleted Successfully',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '500',
                        'message' => 'Failed To Delete Post Store',
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

    public function active($id)
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

            $posts = PostStore::whereIn('id', $idsArray)->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Records Found',
                ], 404);
            }

            $posts->each(function ($post) {
                $post->status = $post->status ? 0 : 1;
                $post->save();
            });

            $message = $posts->first()->status ? 'Post Store Data Activae Successfully' : 'Post Store Data Deactive Successfully';

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => $message,
                // 'data' => $posts->pluck('id')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An Error Occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function convertToSlug($string)
    {
        return str_replace([' ', '_', '/'], '', strtolower($string));
    }
}
