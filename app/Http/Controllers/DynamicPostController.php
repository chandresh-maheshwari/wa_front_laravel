<?php
namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\PostStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DynamicPostController extends Controller
{
    protected $request;
    protected $dynamicPost1;
    public function __construct(Request $request, DynamicPost $dynamicPost)
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
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = DynamicPost::orderBy('id', 'desc')->get();

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
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $this->validate($request, [
                'post_title' => 'required|string|max:255',
                'post_description' => 'required|array',
                'post_type' => 'required|string',
                'ordering' => 'integer|min:1',
            ]);

            $postData = $request['post_description'];
            $postData1 = $request['post_type'];

            $generateSlugs = function (&$data) use (&$generateSlugs) {
                foreach ($data as $key => &$value) {
                    if (is_array($value)) {
                        $generateSlugs($value);
                    } elseif ($key === 'label') {
                        $slug = str_replace(' ', '', $value); 
                        $slugKey = 'field_slug_' . $slug;
                        $data[$slugKey] = $slug;
                    }
                }
            };

            // Apply slug generation to post description
            $generateSlugs($postData);

            $ordering = $request['ordering'] ?? 1;
            if ($ordering == 0) {
                $ordering = 1;
            }

            $saveData = $this->dynamicPost1->savePost([
                'post_title' => $request['post_title'],
                'post_description' => $postData,
                'post_type' => $postData1,
                'ordering' => $ordering,
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
                    'message' => 'Something Went Wrong',
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
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = DynamicPost::where('id', $id)->where('deleted_at', 0)->first();
            if (! $post) {
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
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $data = DynamicPost::where('deleted_at', 0)->find($id);
            if (! $data) {
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

            $oldPostTitle = $post->post_title; 
            $post->post_title = $request['post_title'];

            if ($request->has('post_description')) {
                $newDescription = $request['post_description'];
                $existingDescription = $post->post_description;

                // Function to generate slugs and handle nested objects
                $generateSlugs = function (&$data, $existingData) use (&$generateSlugs) {
                    foreach ($data as $key => &$value) {
                        if (is_array($value)) {
                            // If it's an array (nested object), recursively call the function to update the nested structure
                            $generateSlugs($value, $existingData[$key] ?? []);
                        } elseif ($key === 'label') {
                            $slug = str_replace(' ', '', $value); 
                            $slugKey = 'field_slug_' . $slug;

                            // Preserve existing slug if it exists
                            foreach ($existingData as $existingKey => $existingValue) {
                                if (strpos($existingKey, 'field_slug_') === 0) {
                                    $data[$existingKey] = $existingValue;
                                }
                            }
                        }
                    }
                };

                // Apply slug generation to new description
                $generateSlugs($newDescription, $existingDescription);

                // Update related PostStore names if they exist
                $updatePostStoreLabels = function ($newData, $existingData, $postId) use (&$updatePostStoreLabels) {
                    foreach ($newData as $key => $value) {
                        if (isset($value['label'])) {
                            $oldLabel = $existingData[$key]['label'] ?? null;
                            $newLabel = $value['label'];
                            if ($oldLabel && $oldLabel !== $newLabel) {

                                // Fetch the data from PostStore
                                $postStores = PostStore::where('post_id', $postId)->get();

                                foreach ($postStores as $postStore) {
                                    $postData = $postStore->data;

                                    if (isset($postData[$oldLabel])) {
                                        $postData[$newLabel] = $postData[$oldLabel];
                                        unset($postData[$oldLabel]);
                                    }

                                    // Handle nested objects
                                    $updateNestedLabels = function (&$data) use ($oldLabel, $newLabel, &$updateNestedLabels) {
                                        foreach ($data as $key => &$value) {
                                            if (is_array($value)) {
                                                if (isset($value[$oldLabel])) {
                                                    $value[$newLabel] = $value[$oldLabel];
                                                    unset($value[$oldLabel]);
                                                }
                                                $updateNestedLabels($value);        
                                            }
                                        }
                                    };

                                    $updateNestedLabels($postData);
                                    $postStore->data = $postData;

                                    if (!$postStore->save()) {
                                        Log::error("Failed to save updated PostStore data for post_id: {$postStore->post_id}");
                                    } else {
                                        Log::info("Successfully saved updated PostStore data for post_id: {$postStore->post_id}");
                                    }
                                }
                            }
                        }

                        // Recursively check nested structures
                        if (is_array($value)) {
                            $updatePostStoreLabels($value, $existingData[$key] ?? [], $postId);
                        }
                    }
                };

                // Apply label updates to PostStore
                $updatePostStoreLabels($newDescription, $existingDescription, $id);

                // Update post_description with the new one
                $post->post_description = $newDescription;
            }

            if ($request->has('post_type')) {
                $post->post_type = $request['post_type'];
            }

            if ($request->has('ordering')) {
                $post->ordering = $request['ordering'];
            }

            if ($post->save()) {
                // Update the post_name in the post_store table where it matches the old post title
                if ($oldPostTitle !== $post->post_title) {
                    PostStore::where('post_name', $oldPostTitle)->update(['post_name' => $post->post_title]);
                }

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
            if (! $user) {
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
            if (! $user) {
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

            // Loop through each post and toggle the status
            $posts->each(function ($post) {
                // Toggle the status: if it's 1, set to 0; if it's 0, set to 1
                $post->status = $post->status == 1 ? 0 : 1;
                $post->save();
            });

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

    public function restore($id)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            // Split and filter the IDs
            $ids = explode(',', $id);
            $ids = array_filter($ids);

            // Restore DynamicPost entries
            $restoredCount = DynamicPost::whereIn('id', $ids)->where('deleted_at', 1)->update(['deleted_at' => 0]);

            // Check if any DynamicPost was restored
            if ($restoredCount > 0) {
                // Also restore related PostStore entries if they exist
                $relatedPostStoresRestored = PostStore::whereIn('post_id', $ids)
                    ->where('deleted_at', 1)
                    ->update(['deleted_at' => 0]);

                // Return response including count of restored PostStore data
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Dynamic Post and Related PostStore Data Restored Successfully',
                    'restored_count' => $restoredCount,
                    'related_post_stores_restored' => $relatedPostStoresRestored,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Dynamic Post Found To Restore',
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

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
