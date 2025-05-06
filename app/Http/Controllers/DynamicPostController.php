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
    private function convertToSlug($string)
    {
        return str_replace([' ', '_', '/'], '', ($string));
    }

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
                        $slugKey = 'Field_Slug_' . $this->convertToSlug($slug);
                        // $slugKey = 'field_slug_' . $slug;
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
            $exists = DynamicPost::where('ordering', $ordering)->exists();

            $saveData = $this->dynamicPost1->savePost([
                'post_title' => $request['post_title'],
                'post_description' => $postData,
                'post_type' => $postData1,
                'ordering' => $ordering,
            ]);
         

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'code' => '409',
                    'message' => 'The ordering value already exists. Please choose a different one.',
                ], 409);
            }
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

                // Slugify function: removes spaces and lowercases the label
                $slugify = fn($label) => preg_replace('/\s+/', '', ($label));

                // 🔁 Preserve old section structure
                $preserveOldSectionData = function (&$newData, $existingData) {
                    foreach ($newData as $newKey => &$newSection) {
                        if (!is_array($newSection)) continue;

                        foreach ($existingData as $oldKey => $oldSection) {
                            if (!is_array($oldSection)) continue;

                            $oldFirstField = $oldSection[0] ?? null;
                            $newFirstField = $newSection[0] ?? null;

                            if (
                                $oldFirstField && $newFirstField &&
                                $oldFirstField['type'] === $newFirstField['type'] &&
                                count($oldSection) === count($newSection)
                            ) {

                                foreach ($oldSection as $fieldKey => $fieldValue) {
                                    if (!isset($newSection[$fieldKey]) && $fieldKey !== 'enabled') {
                                        $newSection[$fieldKey] = $fieldValue;
                                    } elseif (is_array($fieldValue) && isset($newSection[$fieldKey])) {
                                        $newSection[$fieldKey] = array_merge($fieldValue, $newSection[$fieldKey]);
                                    }
                                }

                                if (!isset($newSection['enabled']) && isset($oldSection['enabled'])) {
                                    $newSection['enabled'] = $oldSection['enabled'];
                                }

                                break;
                            }
                        }
                    }
                };

                $generateSlugs = function (&$data, $existingData) use (&$generateSlugs, $slugify) {
                    foreach ($data as $key => &$value) {
                        // Recursively go deeper for nested sections
                        if (is_array($value) && !isset($value['label'])) {
                            $generateSlugs($value, $existingData[$key] ?? []);
                            continue;
                        }

                        // Process individual fields that have a 'label'
                        if (isset($value['label'])) {
                            $newSlug = $slugify($value['label']);

                            // Flag to check if any field_slug_ already exists
                            $slugKeyFound = false;

                            foreach ($value as $innerKey => $innerVal) {
                                if (strpos($innerKey, 'Field_Slug_') === 0) {
                                    $value[$innerKey] = $newSlug;
                                    $slugKeyFound = true;
                                }
                            }

                            // If not found, try restoring the same key name from old data
                            if (!$slugKeyFound && isset($existingData[$key])) {
                                foreach ($existingData[$key] as $oldFieldKey => $oldFieldValue) {
                                    if (strpos($oldFieldKey, 'Field_Slug_') === 0) {
                                        $value[$oldFieldKey] = $newSlug;
                                        $slugKeyFound = true;
                                        break;
                                    }
                                }
                            }

                            // If still not found, just create one using new slug as the key name
                            if (!$slugKeyFound) {
                                $value["Field_Slug_" . $newSlug] = $newSlug;
                            }
                        }
                    }
                };


                $updatePostStoreLabels = function ($newData, $existingData, $postId) use ($slugify) {
                    $postStores = PostStore::where('post_id', $postId)->get();

                    foreach ($postStores as $postStore) {
                        $originalPostData = $postStore->data;
                        
                        // First, handle section renames to preserve all section data
                        foreach ($newData as $newSectionKey => $newSection) {
                            if (!is_array($newSection)) continue;

                            // Find corresponding old section by matching type
                            $oldSectionKey = null;
                            foreach ($existingData as $key => $section) {
                                if (is_array($section) && isset($section[0]) && isset($newSection[0]) &&
                                    $section[0]['type'] === $newSection[0]['type']) {
                                    $oldSectionKey = $key;
                                    break;
                                }
                            }

                            // If section exists but was renamed
                            if ($oldSectionKey && $oldSectionKey !== $newSectionKey && isset($originalPostData[$oldSectionKey])) {
                                $originalPostData[$newSectionKey] = $originalPostData[$oldSectionKey];
                                unset($originalPostData[$oldSectionKey]);
                            }
                            
                            // Initialize section if it doesn't exist
                            if (!isset($originalPostData[$newSectionKey])) {
                                $originalPostData[$newSectionKey] = [];
                            }

                            // Process fields within the section
                            if (is_array($newSection)) {
                                foreach ($newSection as $fieldIndex => $newField) {
                                    if (!is_array($newField) || !isset($newField['label']) || !is_string($newField['label'])) continue;

                                    $newLabel = $newField['label'];
                                    $newSlug = $slugify($newLabel);

                                    // Find if this field existed before with a different label
                                    $oldLabel = null;
                                    $oldSlug = null;
                                    if ($oldSectionKey && isset($existingData[$oldSectionKey][$fieldIndex]['label'])) {
                                        $oldLabel = $existingData[$oldSectionKey][$fieldIndex]['label'];
                                        $oldSlug = $slugify($oldLabel);
                                    }

                                    if ($oldLabel && $oldLabel !== $newLabel) {
                                        // This is an existing field being renamed
                                        $originalFieldSlugKey = null;
                                        
                                        // First find the original Field_Slug key for this field
                                        foreach ($originalPostData[$newSectionKey] as $key => $value) {
                                            if (strpos($key, 'Field_Slug_') === 0 && $value === $oldSlug) {
                                                $originalFieldSlugKey = $key;
                                                break;
                                            }
                                        }

                                        // Now update the field and its Field_Slug value
                                        foreach ($originalPostData[$newSectionKey] as $key => $value) {
                                            if ($key === $oldLabel) {
                                                // Update the field key and value
                                                $originalPostData[$newSectionKey][$newLabel] = $value;
                                                unset($originalPostData[$newSectionKey][$key]);
                                            } elseif ($key === $originalFieldSlugKey) {
                                                // Update only this field's Field_Slug value
                                                $originalPostData[$newSectionKey][$key] = $newSlug;
                                            }
                                        }
                                    } elseif (!isset($originalPostData[$newSectionKey][$newLabel])) {
                                        // This is a new field - create new Field_Slug key
                                        $originalPostData[$newSectionKey][$newLabel] = null;
                                        $originalPostData[$newSectionKey]["Field_Slug_" . $newSlug] = $newSlug;
                                    }
                                }
                            }
                        }

                        // Handle top-level fields
                        foreach ($newData as $key => $value) {
                            if (!is_array($value) || !isset($value['label']) || !is_string($value['label'])) continue;

                            $newLabel = $value['label'];
                            $newSlug = $slugify($newLabel);
                            $oldLabel = $existingData[$key]['label'] ?? null;
                            $oldSlug = $oldLabel ? $slugify($oldLabel) : null;

                            if ($oldLabel && $oldLabel !== $newLabel) {
                                // This is an existing field being renamed
                                $originalFieldSlugKey = null;
                                
                                // First find the original Field_Slug key for this field
                                foreach ($originalPostData as $key => $value) {
                                    if (strpos($key, 'Field_Slug_') === 0 && $value === $oldSlug) {
                                        $originalFieldSlugKey = $key;
                                        break;
                                    }
                                }

                                // Now update the field and its Field_Slug value
                                foreach ($originalPostData as $key => $value) {
                                    if ($key === $oldLabel) {
                                        // Update the field key and value
                                        $originalPostData[$newLabel] = $value;
                                        unset($originalPostData[$key]);
                                    } elseif ($key === $originalFieldSlugKey) {
                                        // Update only this field's Field_Slug value
                                        $originalPostData[$key] = $newSlug;
                                    }
                                }
                            } elseif (!isset($originalPostData[$newLabel])) {
                                // This is a new field - create new Field_Slug key
                                $originalPostData[$newLabel] = null;
                                $originalPostData["Field_Slug_" . $newSlug] = $newSlug;
                            }
                        }

                        $originalPostData = array_filter(
                            $originalPostData,
                            fn($val, $key) => !is_numeric($key),
                            ARRAY_FILTER_USE_BOTH
                        );

                        $postStore->data = $originalPostData;
                        $postStore->save();
                    }
                };

                $preserveOldSectionData($newDescription, $existingDescription);
                $generateSlugs($newDescription, $existingDescription);

                // Update slugs and handle field changes
                $updatePostStoreLabels($newDescription, $existingDescription, $id);

                $post->post_description = $newDescription;
            }

            // Update other post fields
            if ($request->has('post_type')) {
                $post->post_type = $request['post_type'];
            }

            if ($request->has('ordering')) {
                $post->ordering = $request['ordering'];
            }

            if ($post->save()) {
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
