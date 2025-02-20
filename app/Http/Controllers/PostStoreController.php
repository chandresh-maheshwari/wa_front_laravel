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
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $postData = PostStore::where('post_name', $postName)
                ->where('deleted_at', 0)
                ->orderBy('id', 'desc')
                ->get();

            if ($postData->isEmpty()) {
                return response()->json([
                    'status'  => true,
                    'code'    => '200',
                    'message' => 'No Post Store Data Found',
                    'results' => [],
                ], 200);
            }

            $postData->transform(function ($post) {
                $data = $post->data;
                foreach ($data as $key => $value) {
                    if (stripos($key, 'field_slug_') !== false) {
                        continue;
                    }

                    // Check if the value is a potential image file name
                    if ($this->isImageFileName($value)) {
                        $data[$key] = URL::to('/uploads/dynamic_post_store/' . $value);
                    }
                }
                $post->data = $data;
                return $post;
            });

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $postData,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error in getList method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    private function isImageFileName($value)
    {
        // Ensure the value is a string before processing
        if (!is_string($value)) {
            return false;
        }

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $imageExtensions);
    }




    /** Function used for the post value store in the database create by ns */

    // public function postStore(Request $request, $postTitle)
    // {
    //     try {
    //         $user = Auth::user()->id;
    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '401',
    //                 'message' => 'User Not Authenticated',
    //             ], 401);
    //         }

    //         $postData = DynamicPost::where('post_title', $postTitle)->first();

    //         if (!$postData) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Post Store Data Not Found',
    //             ], 404);
    //         }

    //         $requestData = $request->all();
    //         $transformedRequest = [];
    //         foreach ($requestData as $key => $value) {
    //             $originalKey = str_replace('_', ' ', $key);
    //             $transformedRequest[$originalKey] = $value;
    //         }

    //         $requiredFields = [];
    //         $labelMap = [];
    //         foreach ($postData->post_description as $field) {
    //             $originalLabel = $field['label'];
    //             $slugLabel = $this->convertToSlug($originalLabel);
    //             $labelMap[$originalLabel] = $slugLabel;

    //             if ($field['type'] === 'file') {
    //                 $requiredFields[$originalLabel] = 'nullable|file|mimes:jpeg,png,gif,svg|dimensions:max_width=1600,max_height=1600|dimensions:min_width=40,min_height=40';
    //             } else {
    //                 $requiredFields[$originalLabel] = 'nullable|string';
    //             }
    //         }

    //         $validateRequest = Validator::make($transformedRequest, $requiredFields);

    //         if ($validateRequest->fails()) {
    //             Log::error('Validation failed', $validateRequest->errors()->toArray());
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'errors' => $validateRequest->errors()
    //             ], 404);
    //         }

    //         $data = [];

    //         foreach ($postData->post_description as $field) {
    //             $originalLabel = $field['label'];
    //             $value = $transformedRequest[$originalLabel] ?? $request->input(str_replace(' ', '_', $originalLabel));

    //             if ($value === null) {
    //                 continue;
    //             }

    //             if ($field['type'] === 'file' && $request->hasFile(str_replace(' ', '_', $originalLabel))) {
    //                 $file = $request->file(str_replace(' ', '_', $originalLabel));
    //                 $originalName = $file->getClientOriginalName();

    //                 $file->move(public_path('uploads/dynamic_post_store'), $originalName);

    //                 $value = $originalName;
    //             }

    //             $data[$originalLabel] = $value;
    //             $data['field_slug_' . $this->convertToSlug($originalLabel)] = $labelMap[$originalLabel];
    //         }

    //         $postData = PostStore::create([
    //             'post_name' => $postTitle,
    //             'post_id' => $postData->id,
    //             'data' => $data,
    //         ]);

    //         if ($postData) {
    //             return response()->json([
    //                 'status' => true,
    //                 'code' => '200',
    //                 'message' => 'Post Store Data Added Successfully',
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Something Went Wrong'
    //             ], 404);
    //         }
    //     } catch (Exception $e) {
    //         Log::error('Error storing post', ['error' => $e->getMessage()]);
    //         return response()->json([
    //             'status' => false,
    //             'code' => '500',
    //             'message' => 'Internal Server Error',
    //         ], 500);
    //     }
    // }
    // public function postStore(Request $request, $postTitle)
    // {
    //     try {
    //         $user = Auth::user()->id;
    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '401',
    //                 'message' => 'User Not Authenticated',
    //             ], 401);
    //         }

    //         $postData = DynamicPost::where('post_title', $postTitle)->first();

    //         if (!$postData) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Post Store Data Not Found',
    //             ], 404);
    //         }

    //         $requestData = $request->all();
    //         $transformedRequest = [];

    //         foreach ($requestData as $key => $value) {
    //             if (strpos($key, 'Section_image_') === 0) {
    //                 continue;
    //             }

    //             if ($this->isJson($value)) {
    //                 $sectionData = json_decode($value, true);
    //                 $sectionTransformed = [];
    //                 foreach ($sectionData as $sectionKey => $sectionValue) {
    //                     $sectionTransformed[$sectionKey] = $sectionValue;
    //                     $slugKey = 'field_slug_' . $this->convertToSlug($sectionKey);
    //                     $sectionTransformed[$slugKey] = $this->convertToSlug($sectionKey);
    //                 }
    //                 // $sectionTransformed = $this->removeFakePaths($sectionTransformed);
    //                 $transformedRequest[$key] = $sectionTransformed;
    //             } else {
    //                 $labelKey = str_replace('_', ' ', $key);
    //                 $transformedRequest[$labelKey] = $value;
    //                 $transformedRequest['field_slug_' . $this->convertToSlug($key)] = $this->convertToSlug($key);
    //             }
    //         }

    //         // Create the PostStore entry first to get the ID
    //         $postStore = PostStore::create([
    //             'post_name' => $postTitle,
    //             'post_id' => $postData->id,
    //             'data' => $transformedRequest,
    //         ]);

    //         // Now use the PostStore ID for the image file name
    //         foreach ($request->files as $key => $file) {
    //             if ($file->isValid()) {
    //                 $destinationPath = public_path('uploads/dynamic_post_store');
    //                 $originalName = $file->getClientOriginalName();
    //                 $extension = $file->getClientOriginalExtension();
    //                 $fileName = $postTitle . '_' . $postStore->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
    //                 $file->move($destinationPath, $fileName);

    //                 if (preg_match('/Section_(\d+)_(.+)/', $key, $matches)) {
    //                     $sectionIndex = $matches[1];
    //                     $fieldName = $matches[2];
    //                     $sectionKey = "Section_$sectionIndex";
    //                     if (isset($transformedRequest[$sectionKey])) {
    //                         $transformedRequest[$sectionKey][$fieldName] = $fileName;
    //                     }
    //                 } else {
    //                     $labelKey = str_replace('_', ' ', $key);
    //                     $transformedRequest[$labelKey] = $fileName;
    //                 }
    //             }
    //         }

    //         // Update the PostStore data with the new file names
    //         $postStore->data = $transformedRequest;
    //         $postStore->save();

    //         if ($postStore) {
    //             return response()->json([
    //                 'status' => true,
    //                 'code' => '200',
    //                 'message' => 'Post Store Data Added Successfully',
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Something Went Wrong'
    //             ], 404);
    //         }
    //     } catch (Exception $e) {
    //         Log::error('Error storing post', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json([
    //             'status' => false,
    //             'code' => '500',
    //             'message' => 'Internal Server Error',
    //         ], 500);
    //     }
    // }


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

            Log::info([$requestData]);
            foreach ($requestData as $reqDatakey => $value) {
                if (strpos($reqDatakey, 'Section_image_') === 0) {
                    continue; // Skip fields starting with Section_image_
                }
                Log::info("IN LOOP");
                Log::info("keys==" . $reqDatakey);
                Log::info([$value]);

                if (is_numeric($value)) {
                    $transformedRequest[$reqDatakey] = (string)$value;
                } else if ($this->isJson($value)) {
                    $sectionData = json_decode($value, true);
                    if (is_array($sectionData)) {
                        Log::info("keys== IN SECTION" . $reqDatakey);
                        $sectionTransformed = [];
                        foreach ($sectionData as $sectionKey => $sectionValue) {
                            $sectionTransformed[$sectionKey] = $sectionValue;
                            $slugKey = 'field_slug_' . $this->convertToSlug($sectionKey);
                            $sectionTransformed[$slugKey] = $this->convertToSlug($sectionKey);
                        }
                        $transformedRequest[$reqDatakey] = $sectionTransformed;
                    }
                } else {
                    Log::info("KEY normal==" . $reqDatakey);
                    $labelKey = str_replace("_", " ", $reqDatakey);
                    Log::info("KEY new ==" . $labelKey);
                    $transformedRequest[$labelKey] = (string)$value;
                    $transformedRequest['field_slug_' . $this->convertToSlug($reqDatakey)] = $this->convertToSlug($reqDatakey);
                }
            }

            // Log the transformed request to check data
            Log::info([$transformedRequest]);


            // Create the PostStore entry first to get the ID
            $postStore = PostStore::create([
                'post_name' => $postTitle,
                'post_id' => $postData->id,
                'data' => $transformedRequest,
            ]);

            Log::info([$transformedRequest]);
            // Now use the PostStore ID for the image file name
            // exit;

            Log::info([$request->files]);
            foreach ($request->files as $key => $file) {
                if (strpos($key, 'Section_image_') === 0) {
                    continue; // Skip files with keys starting with Section_image_
                }

                if ($file->isValid()) {
                    $destinationPath = public_path('uploads/dynamic_post_store');
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $postname = str_replace(' ', '_', $postTitle);
                    Log::info('ssssssssssssssssssssssssssssssssssssssssss');
                    Log::info($key);
                    $fileNameOuter = $postname . '_' . $postStore->id . '_' . $key . '.' . $extension;
                    // $fileNameOuter = $postname . '_' . $postStore->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                    $file->move($destinationPath, $fileNameOuter);


                    // If not updated in a section, update as a normal field
                    // if (!$updated) {
                    $labelKey = str_replace('_', ' ', $key);
                    $transformedRequest[$labelKey] = $fileNameOuter;
                    $transformedRequest['field_slug_' . $this->convertToSlug($key)] = $this->convertToSlug($key);
                    // }
                }
            }


            // Check if the file belongs to a section
            $updated = false;
            Log::info('out foreach');
            foreach ($transformedRequest as $sectionKey => $sectionValue) {
                Log::info($sectionValue);

                // if(is_array($sectionValue)){
                //     Log::info('is array');
                // }else{
                //     Log::info('not arrray');
                // }
                // if(array_key_exists($key, $sectionValue)){
                //     Log::info('aaaaaaaaaaaaaaa');
                // }else{
                //     Log::info('bbbbbbbbbbbb');
                // }


                Log::info("out of is array");
                if (is_array($sectionValue) && array_key_exists($sectionKey, $transformedRequest)) {
                    Log::info("IN IF CONDITION" . $sectionKey);
                    foreach ($request->files as $sectionkeyFile => $sectionfile) {
                        Log::info("IN FOREACH CONDITION");
                        if (strpos($sectionkeyFile, $sectionKey) > 0) {

                            Log::info("Section key==");
                            Log::info($sectionKey);
                            Log::info("Section key with underscor==");
                            Log::info($sectionkeyFile);

                            if ($sectionfile->isValid()) {
                                $destinationPath = public_path('uploads/dynamic_post_store');
                                $originalName = $sectionfile->getClientOriginalName();
                                $extension = $sectionfile->getClientOriginalExtension();
                                
                                // Check if the file belongs to a section
                                $updated = false;
                                
                                $fieldname = str_replace("_", " ", str_replace("Section_image_" . $sectionKey . "_", "", $sectionkeyFile));                                
                                $fieldnameforimg = str_replace(' ', '_', $fieldname);
                                $postname = str_replace(' ', '_', $postTitle);
                                $fileName = $postname . '_' . $postStore->id . '_' . $fieldnameforimg . '.' . $extension;
                                // $fileName = $postname . '_' . $postStore->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                                $sectionfile->move($destinationPath, $fileName);


                                
                                // Log::info("Field name after remove str=");
                                Log::info("filename==" . $fieldname);

                                if ($fieldname != "") {
                                    // $sectionIndex = $matches[1];
                                    // $fieldName = $matches[2];
                                    // $sectionKey = "Section_$sectionIndex";
                                    log::info('AAAAAAAAAAAAAAAAAAAAAAAAAAAA');
                                    log::info($sectionKey);
                                    if (isset($transformedRequest[$sectionKey])) {
                                        $transformedRequest[$sectionKey][$fieldname] = $fileName;
                                    }
                                }
                                // else {
                                //     $labelKey = str_replace('_', ' ', $sectionkeyFile);
                                //     $transformedRequest[$labelKey] = $fileName;
                                //     $transformedRequest['field_slug_' . $this->convertToSlug($sectionkeyFile)] = $this->convertToSlug($sectionkeyFile);
                                // }
                                // Log::info("IN is array");
                                // Log::info($sectionValue);
                                // Log::info($key);
                                // $sectionValue[$key] = $fileName;
                                // $sectionValue['field_slug_' . $key] = $this->convertToSlug($key);
                                // $updated = true;
                                // break;
                            }
                            // continue; // Skip files with keys starting with Section_image_
                        }
                    }
                }
            }

            // Update the PostStore data with the new file names
            $postStore->data = $transformedRequest;
            $postStore->save();

            if ($postStore) {
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
            Log::error('Error storing post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }







    // public function postStore(Request $request, $postTitle)
    // {
    //     try {
    //         $user = Auth::user()->id;
    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '401',
    //                 'message' => 'User Not Authenticated',
    //             ], 401);
    //         }

    //         $postData = DynamicPost::where('post_title', $postTitle)->first();

    //         if (!$postData) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Post Store Data Not Found',
    //             ], 404);
    //         }

    //         $requestData = $request->all();
    //         $transformedRequest = [];

    //         foreach ($requestData as $key => $value) {
    //             $labelKey = str_replace('_', ' ', $key);

    //             if (is_array($value)) {
    //                 $transformedRequest[$labelKey] = json_encode($value);
    //             } else {
    //                 if (is_numeric($value)) {
    //                     $value = $value + 0; 
    //                 }
    //                 $transformedRequest[$labelKey] = $value;
    //             }

    //             $transformedRequest['field_slug_' . $this->convertToSlug($key)] = $this->convertToSlug($key);
    //         }

    //         // Create the PostStore entry first
    //         $postStore = PostStore::create([
    //             'post_name' => $postTitle,
    //             'post_id' => $postData->id,
    //             'data' => $transformedRequest,
    //         ]);

    //         if (!$postStore) {
    //             return response()->json([
    //                 'status' => false,
    //                 'code' => '404',
    //                 'message' => 'Something Went Wrong'
    //             ], 404);
    //         }

    //         // Now use the PostStore id for the image file name
    //         foreach ($request->files as $key => $file) {
    //             if ($file->isValid()) {
    //                 $destinationPath = public_path('uploads/dynamic_post_store');

    //                 // Create a new file name with the format postname_poststoreid_imagename.extension
    //                 $originalName = $file->getClientOriginalName();
    //                 $extension = $file->getClientOriginalExtension();
    //                 $fileName = $postTitle . '_' . $postStore->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;

    //                 $file->move($destinationPath, $fileName);

    //                 $labelKey = str_replace('_', ' ', $key);
    //                 $transformedRequest[$labelKey] = $fileName;
    //             }
    //         }

    //         foreach ($requestData as $key => $value) {
    //             if (is_array($value)) {
    //                 $sectionData = $value;
    //                 foreach ($sectionData as $sectionKey => $sectionValue) {
    //                     // Check if the section key corresponds to a file input
    //                     if ($request->hasFile($sectionKey)) {
    //                         $file = $request->file($sectionKey);
    //                         if ($file->isValid()) {
    //                             $destinationPath1 = public_path('uploads/dynamic_post_store');

    //                             // Create a new file name with the format postname_poststoreid_imagename.extension
    //                             $originalName = $file->getClientOriginalName();
    //                             $extension = $file->getClientOriginalExtension();
    //                             $fileName = $postTitle . '_' . $postStore->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;

    //                             // Move the file to the destination path
    //                             $file->move($destinationPath1, $fileName);

    //                             // Update the section data with the new file name
    //                             $sectionData[$sectionKey] = $fileName;
    //                         } else {
    //                             Log::error('Invalid file for section key: ' . $sectionKey);
    //                         }
    //                     } else {
    //                         Log::info('No file found for section key: ' . $sectionKey);
    //                     }
    //                 }
    //                 // Ensure the transformedRequest is updated with the correct section data
    //                 $transformedRequest[$key] = json_encode($sectionData);
    //             }
    //         }

    //         // Update the PostStore data with the new file names
    //         $postStore->data = $transformedRequest;
    //         $postStore->save();

    //         return response()->json([
    //             'status' => true,
    //             'code' => '200',
    //             'message' => 'Post Store Data Added Successfully',
    //         ], 200);
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         Log::error('Database Query Error', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'request_data' => $request->all()
    //         ]);
    //         return response()->json([
    //             'status' => false,
    //             'code' => '500',
    //             'message' => 'Database Error',
    //         ], 500);
    //     } catch (Exception $e) {
    //         Log::error('General Error', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'request_data' => $request->all()
    //         ]);
    //         return response()->json([
    //             'status' => false,
    //             'code' => '500',
    //             'message' => 'Internal Server Error',
    //         ], 500);
    //     }
    // }
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Recursively update file paths in nested arrays.
     */
    // private function removeFakePaths(array $data): array
    // {
    //     foreach ($data as $key => $value) {
    //         if (is_array($value)) {
    //             // If the value is an array, recurse into it
    //             $data[$key] = $this->removeFakePaths($value);
    //         } elseif (strpos($value, 'C:\\fakepath\\') !== false) {
    //             // If it's a string with a fake path, just store the basename
    //             $data[$key] = basename($value);
    //         }
    //     }
    //     return $data;
    // }

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

            $post = PostStore::where('id', $id)->first();

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

            $postArray = $post->toArray();
            $dataArray = $postArray['data'];

            foreach ($dataArray as $key => $value) {
                if (is_array($value)) {
                    // If the value is an array, it might be a section
                    foreach ($value as $sectionKey => $sectionValue) {
                        if ($this->isImageFileName($sectionValue)) {
                            $dataArray[$key][$sectionKey] = URL::to('/uploads/dynamic_post_store/' . $sectionValue);
                        }
                    }
                } else {
                    if ($this->isImageFileName($value)) {
                        $dataArray[$key] = URL::to('/uploads/dynamic_post_store/' . $value);
                    }
                }
            }

            $postArray['data'] = $dataArray;

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => [
                    'data' => $postArray,
                ],
            ], 200);
        } catch (Exception $e) {
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
            Log::info('Update function called', ['id' => $id]);

            $user = Auth::user();
            if (!$user) {
                Log::warning('User not authenticated');
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            // Retrieve the post to update
            $post = PostStore::where('id', $id)->where('deleted_at', 0)->first();
            if (!$post) {
                Log::warning('Post not found', ['id' => $id]);
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            // Retrieve the related DynamicPost data
            $postData = DynamicPost::where('id', $post->post_id)->first();
            if (!$postData) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Data Not Found',
                ], 404);
            }

            $requestData = $request->all();
            $transformedRequest = [];

            // Process all fields except files
            foreach ($requestData as $key => $value) {
                if (strpos($key, 'Section_image_') === 0) {
                    continue; // Skip fields starting with Section_image_
                }

                if (is_numeric($value)) {
                    $transformedRequest[$key] = (string)$value;
                } else if ($this->isJson($value)) {
                    // Handle fields with JSON data (sections)
                    $sectionData = json_decode($value, true);
                    if (is_array($sectionData)) {
                        $sectionTransformed = [];
                        foreach ($sectionData as $sectionKey => $sectionValue) {
                            $sectionTransformed[$sectionKey] = $sectionValue;
                            $slugKey = 'field_slug_' . $this->convertToSlug($sectionKey);
                            $sectionTransformed[$slugKey] = $this->convertToSlug($sectionKey);
                        }
                        $transformedRequest[$key] = $sectionTransformed;
                    }
                } else {
                    // For other fields
                    $labelKey = str_replace('_', ' ', $key);
                    $transformedRequest[$labelKey] = $value;
                    $transformedRequest['field_slug_' . $this->convertToSlug($key)] = $this->convertToSlug($key);
                }
            }

            // Handle file uploads
            foreach ($request->files as $key => $file) {
                if (strpos($key, 'Section_image_') === 0) {
                    continue; // Skip files with keys starting with Section_image_
                }

                if ($file->isValid()) {
                    $destinationPath = public_path('uploads/dynamic_post_store');
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileNameOuter = $post->post_name . '_' . $post->id  . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                    $file->move($destinationPath, $fileNameOuter);



                    // If not updated in a section, update as a normal field
                    // if (!$updated) {
                    $labelKey = str_replace('_', ' ', $key);
                    $transformedRequest[$labelKey] = $fileNameOuter;
                    $transformedRequest['field_slug_' . $this->convertToSlug($key)] = $this->convertToSlug($key);
                    // }
                }
            }

            $updated = false;
            Log::info('out foreach');
            foreach ($transformedRequest as $sectionKey => $sectionValue) {
                Log::info($sectionValue);

                // if(is_array($sectionValue)){
                //     Log::info('is array');
                // }else{
                //     Log::info('not arrray');
                // }
                // if(array_key_exists($key, $sectionValue)){
                //     Log::info('aaaaaaaaaaaaaaa');
                // }else{
                //     Log::info('bbbbbbbbbbbb');
                // }


                Log::info("out of is array");
                if (is_array($sectionValue) && array_key_exists($sectionKey, $transformedRequest)) {
                    Log::info("IN IF CONDITION" . $sectionKey);
                    foreach ($request->files as $sectionkeyFile => $sectionfile) {
                        Log::info("IN FOREACH CONDITION");
                        if (strpos($sectionkeyFile, $sectionKey) > 0) {

                            Log::info("Section key==");
                            Log::info($sectionKey);
                            Log::info("Section key with underscor==");
                            Log::info($sectionkeyFile);

                            if ($sectionfile->isValid()) {
                                $destinationPath = public_path('uploads/dynamic_post_store');
                                $originalName = $sectionfile->getClientOriginalName();
                                $extension = $sectionfile->getClientOriginalExtension();
                                $fileName = $post->post_name . '_' . $post->id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                                $sectionfile->move($destinationPath, $fileName);

                                // Check if the file belongs to a section
                                $updated = false;

                                // $fieldname = str_replace("Section_image_" . $sectionKey . "_", "", $sectionkeyFile);
                                $fieldname = str_replace("_", " ", str_replace("Section_image_" . $sectionKey . "_", "", $sectionkeyFile));


                                // Log::info("Field name after remove str=");
                                Log::info("filename==" . $fieldname);

                                if ($fieldname != "") {
                                    // $sectionIndex = $matches[1];
                                    // $fieldName = $matches[2];
                                    // $sectionKey = "Section_$sectionIndex";
                                    log::info('AAAAAAAAAAAAAAAAAAAAAAAAAAAA');
                                    log::info($sectionKey);
                                    if (isset($transformedRequest[$sectionKey])) {
                                        $transformedRequest[$sectionKey][$fieldname] = $fileName;
                                    }
                                }
                                // else {
                                //     $labelKey = str_replace('_', ' ', $sectionkeyFile);
                                //     $transformedRequest[$labelKey] = $fileName;
                                //     $transformedRequest['field_slug_' . $this->convertToSlug($sectionkeyFile)] = $this->convertToSlug($sectionkeyFile);
                                // }
                                // Log::info("IN is array");
                                // Log::info($sectionValue);
                                // Log::info($key);
                                // $sectionValue[$key] = $fileName;
                                // $sectionValue['field_slug_' . $key] = $this->convertToSlug($key);
                                // $updated = true;
                                // break;
                            }
                            // continue; // Skip files with keys starting with Section_image_
                        }
                    }
                }
            }

            // Update the PostStore data with the new file names
            $post->data = $transformedRequest;
            $post->save();
            // Update the post data
            // $post->data = $existingData;

            // Save the updated post data
            if ($post->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Post Store Data Updated Successfully',
                ], 200);
            } else {
                Log::error('Failed to update post', ['id' => $id]);
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Update Post Store',
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Error updating post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }


    //  private function isJson($string) {
    //      json_decode($string);
    //      return (json_last_error() == JSON_ERROR_NONE);
    //  }


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

     

public function active(Request $request, $id)
{
    try {
        // Log the user and request data
        Log::info('User:', ['user' => Auth::user()]); 
        Log::info('Request Data:', ['id' => $id, 'status' => $request->input('status')]);
        
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $idsArray = explode(',', $id);
        Log::info('IDs Array:', ['ids' => $idsArray]);

        // Validation
        $validatedData = Validator::make(
            ['ids' => $idsArray],
            ['ids' => 'required|array|min:1'],
            ['ids.*' => 'integer|exists:post_stores,id']
        );

        if ($validatedData->fails()) {
            return response()->json([
                'status' => false,
                'code' => '422',
                'message' => 'Validation Failed',
                'errors' => $validatedData->errors(),
            ], 422);
        }

        $posts = postStore::whereIn('id', $idsArray)->get();
        Log::info('Fetched Posts:', ['posts' => $posts]);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'No Records Found',
            ], 404);
        }

        $newStatus = $request->input('status');
        Log::info('New Status:', ['status' => $newStatus]);

        // Ensure the status is either 0 or 1
        if ($newStatus !== null && in_array($newStatus, [0, 1])) {
            // Update status in batch
            postStore::whereIn('id', $idsArray)->update(['status' => $newStatus]);

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => '422',
                'message' => 'Invalid Status Value',
            ], 422);
        }

    } catch (\Exception $e) {
        Log::error('Error occurred:', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => false,
            'code' => '500',
            'message' => 'An Error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /** 
     * Delete the  image found in a post by its ID.
     * Ensures the user is authenticated before deleting the image. create by ns
     */
    public function deleteImage($id, $imageName)
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

            $post = PostStore::where('id', $id)->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            $data = $post->data;
            $imageDeleted = false;

            $normalizedImageName = trim($imageName);
            $normalizedImageNameLower = strtolower($normalizedImageName);

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    // Handle nested arrays
                    foreach ($value as $subKey => $subValue) {
                        $normalizedSubValue = strtolower(trim($subValue));
                        if ($normalizedSubValue === $normalizedImageNameLower) {
                            $data[$key][$subKey] = ""; // Remove the image reference
                            $imageDeleted = true;
                            break 2; // Exit both loops
                        }
                    }
                } else {
                    $normalizedValue = strtolower(trim($value));
                    if ($normalizedValue === $normalizedImageNameLower) {
                        $data[$key] = ""; // Remove the image reference
                        $imageDeleted = true;
                        break;
                    }
                }
            }

            if (!$imageDeleted) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Image Found',
                ], 404);
            }

            $post->data = $data;
            $post->save();

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Image Reference Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting image', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    private function convertToSlug($string)
    {
        return str_replace([' ', '_', '/'], '', strtolower($string));
    }

    public function addData(Request $request)
    {
        try {
            // Assuming the data is sent as a JSON string in the request body
            $inputData = $request->input('data');

            // Parse the input data
            $parsedData = $this->parseInputData($inputData);

            // Validate the data
            $validator = Validator::make($parsedData, [
                // Define your validation rules here
                'field_name' => 'required|string',
                // Add more rules as needed
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'code' => '422',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Insert the data into the database
            $postStore = PostStore::create($parsedData);

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Data added successfully',
                'data' => $postStore,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error adding data', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function parseInputData($inputData)
    {
        $parsedData = [];
        // Implement your parsing logic here    
        // For example, you might use regex or JSON decoding
        // to extract key-value pairs from the input data

        return $parsedData;
    }


    public function restore($id)
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

        $restoredCount = PostStore::whereIn('id', $ids)->where('deleted_at', 1)->update(['deleted_at' => 0]);

        if ($restoredCount > 0) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Post Store Data Restored Successfully',
                'restored_count' => $restoredCount,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'No Post Store Found To Restore',
            ], 404);
        }
    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'code' => '500',
            'message' => 'An Error Occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
