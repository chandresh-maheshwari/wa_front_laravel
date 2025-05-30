<?php
namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\PostStore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

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
                    if (stripos($key, 'Field_Slug_') !== false) {
                        continue;
                    }

                    if ($this->isImageFileName($value)) {
                        $data[$key] = URL::to('/uploads/dynamic_post_store/' . $value);
                    } elseif ($this->isFileName($value)) {
                        $data[$key] = basename($value);
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
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function getPostData($post_id)
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

            $postData = PostStore::where('post_id', $post_id)
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

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $postData,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error in getList method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
        if (!is_string($value)) {
            return false;
        }
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $allowedExtensions);
    }

    private function isFileName($value)
    {
        if (!is_string($value)) {
            return false;
        }
        $allowedExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg',
            'pdf', 'xls', 'xlsx', 'doc', 'docx', 'txt'
        ];
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $allowedExtensions);
    }

    public function postStore(Request $request, $postTitle)
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

            $postData = DynamicPost::where('post_title', $postTitle)->first();

            if (! $postData) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            // First create the post store to get the ID
            $postStore = PostStore::create([
                'post_name' => $postTitle,
                'post_id'   => $postData->id,
                'data'      => [],
            ]);

            $requestData = $request->all();
            $transformedRequest = [];

            foreach ($requestData as $reqDatakey => $value) {
                try {
                    if (strpos($reqDatakey, 'Section ') === 0) {
                        if (strpos($reqDatakey, 'Section_image_') === 0) {
                            continue;
                        }

                        if ($this->isJson($value)) {
                            if (is_string($value)) {
                                $sectionData = json_decode($value, true);
                                if (is_array($sectionData)) {
                                    $sectionTransformed = [];
                                    foreach ($sectionData as $sectionKey => $sectionValue) {
                                        // Handle base64 files inside sections
                                        if (is_string($sectionValue) && strpos($sectionValue, 'data:') === 0) {
                                            // Extract file type and generate filename
                                            preg_match('/data:(.*?);/', $sectionValue, $matches);
                                            $mimeType = $matches[1] ?? '';
                                            
                                            // Map MIME types to extensions
                                            $mimeToExt = [
                                                'image/jpeg' => 'jpg',
                                                'image/png' => 'png',
                                                'image/gif' => 'gif',
                                                'image/bmp' => 'bmp',
                                                'image/svg+xml' => 'svg',
                                                'application/pdf' => 'pdf',
                                                'application/vnd.ms-excel' => 'xls',
                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                                                'application/msword' => 'doc',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                                                'text/plain' => 'txt'
                                            ];

                                            $extension = $mimeToExt[$mimeType] ?? 'bin';
                                            
                                            // Generate filename with poststore ID and section name
                                            $postname = str_replace(' ', '_', $postTitle);
                                            $sectionName = str_replace(' ', '_', $reqDatakey);
                                            $fileName = $postname . '_' . $postStore->id . '_' . $sectionName . '_' . $sectionKey . '.' . $extension;
                                            
                                            // Save only the filename in the section
                                            $sectionTransformed[$sectionKey] = $fileName;
                                            
                                            // Save the actual file
                                            list($type, $base64Data) = explode(';', $sectionValue);
                                            list(, $base64Data) = explode(',', $base64Data);
                                            $fileData = base64_decode($base64Data);
                                            
                                            $destinationPath = public_path('uploads/dynamic_post_store');
                                            if (!file_exists($destinationPath)) {
                                                mkdir($destinationPath, 0777, true);
                                            }
                                            
                                            file_put_contents($destinationPath . '/' . $fileName, $fileData);
                                        } else {
                                            $sectionTransformed[$sectionKey] = $sectionValue;
                                        }
                                        $slugKey = 'Field_Slug_' . $this->convertToSlug($sectionKey);
                                        $sectionTransformed[$slugKey] = $this->convertToSlug1($sectionKey);
                                    }
                                    $transformedRequest[$reqDatakey] = $sectionTransformed;
                                }
                            }
                        } elseif (is_array($value)) {
                            $sectionData = $value;
                            $sectionTransformed = [];
                            foreach ($sectionData as $sectionKey => $sectionValue) {
                                // Handle base64 files inside sections
                                if (is_string($sectionValue) && strpos($sectionValue, 'data:') === 0) {
                                    // Extract file type and generate filename
                                    preg_match('/data:(.*?);/', $sectionValue, $matches);
                                    $mimeType = $matches[1] ?? '';
                                    
                                    // Map MIME types to extensions
                                    $mimeToExt = [
                                        'image/jpeg' => 'jpg',
                                        'image/png' => 'png',
                                        'image/gif' => 'gif',
                                        'image/bmp' => 'bmp',
                                        'image/svg+xml' => 'svg',
                                        'application/pdf' => 'pdf',
                                        'application/vnd.ms-excel' => 'xls',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                                        'application/msword' => 'doc',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                                        'text/plain' => 'txt'
                                    ];

                                    $extension = $mimeToExt[$mimeType] ?? 'bin';
                                    
                                    // Generate filename with poststore ID and section name
                                    $postname = str_replace(' ', '_', $postTitle);
                                    $sectionName = str_replace(' ', '_', $reqDatakey);
                                    $fileName = $postname . '_' . $postStore->id . '_' . $sectionName . '_' . $sectionKey . '.' . $extension;
                                    
                                    // Save only the filename in the section
                                    $sectionTransformed[$sectionKey] = $fileName;
                                    
                                    // Save the actual file
                                    list($type, $base64Data) = explode(';', $sectionValue);
                                    list(, $base64Data) = explode(',', $base64Data);
                                    $fileData = base64_decode($base64Data);
                                    
                                    $destinationPath = public_path('uploads/dynamic_post_store');
                                    if (!file_exists($destinationPath)) {
                                        mkdir($destinationPath, 0777, true);
                                    }
                                    
                                    file_put_contents($destinationPath . '/' . $fileName, $fileData);
                                } else {
                                    $sectionTransformed[$sectionKey] = $sectionValue;
                                }
                                $slugKey = 'Field_Slug_' . $this->convertToSlug($sectionKey);
                                $sectionTransformed[$slugKey] = $this->convertToSlug1($sectionKey);
                            }
                            $transformedRequest[$reqDatakey] = $sectionTransformed;
                        }
                    } else {
                        // Handle base64 files outside sections
                        if (is_string($value) && strpos($value, 'data:') === 0) {
                            // Extract file type and generate filename
                            preg_match('/data:(.*?);/', $value, $matches);
                            $mimeType = $matches[1] ?? '';
                            
                            // Map MIME types to extensions
                            $mimeToExt = [
                                'image/jpeg' => 'jpg',
                                'image/png' => 'png',
                                'image/gif' => 'gif',
                                'image/bmp' => 'bmp',
                                'image/svg+xml' => 'svg',
                                'application/pdf' => 'pdf',
                                'application/vnd.ms-excel' => 'xls',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                                'application/msword' => 'doc',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                                'text/plain' => 'txt'
                            ];

                            $extension = $mimeToExt[$mimeType] ?? 'bin';
                            
                            // Generate filename with poststore ID
                            $postname = str_replace(' ', '_', $postTitle);
                            $fileName = $postname . '_' . $postStore->id . '_' . $reqDatakey . '.' . $extension;
                            
                            // Save only the filename in the transformed request
                            $transformedRequest[$reqDatakey] = $fileName;
                            $transformedRequest['Field_Slug_' . $this->convertToSlug($reqDatakey)] = $this->convertToSlug1($reqDatakey);
                            
                            // Save the actual file
                            list($type, $base64Data) = explode(';', $value);
                            list(, $base64Data) = explode(',', $base64Data);
                            $fileData = base64_decode($base64Data);
                            
                            $destinationPath = public_path('uploads/dynamic_post_store');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0777, true);
                            }
                            
                            file_put_contents($destinationPath . '/' . $fileName, $fileData);
                            continue;
                        }

                        if (strpos($reqDatakey, 'Section_image_') === 0) {
                            continue;
                        }

                        if (is_array($value)) {
                            $transformedRequest[$reqDatakey] = $value;
                            $slugKey = 'Field_Slug_' . $this->convertToSlug($reqDatakey);
                            $transformedRequest[$slugKey] = $this->convertToSlug1($reqDatakey);
                        } else {
                            $labelKey = $reqDatakey;
                            $transformedRequest[$labelKey] = (string) $value;
                            $transformedRequest['Field_Slug_' . $this->convertToSlug($reqDatakey)] = $this->convertToSlug1($reqDatakey);
                        }
                    }
                } catch (Exception $e) {
                    Log::error('Error processing request data item', [
                        'key' => $reqDatakey,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update the post store with the transformed data
            $postStore->data = $transformedRequest;
            $postStore->save();

            if ($postStore) {
                return response()->json([
                    'status'  => true,
                    'code'    => '200',
                    'message' => 'Post Store Data Added Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Something Went Wrong', 
                ], 404);
            }
        } catch (Exception $e) {
            Log::error('Error in postStore method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'postTitle' => $postTitle,
                'requestData' => $request->all()
            ]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function isJson($string)
    {
        if (! is_string($string)) {
            return false; // Return false if $string is not a string
        }

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Display a specific post by its postName.
     * Ensures the post is not deleted before displaying. create by ns
     */
    public function show($postName)
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

            $post = PostStore::where('post_name', $postName)->first();

            if (! $post) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            if ($post->deleted_at != 0) {
                return response()->json([
                    'status'  => false,
                    'code'    => '410',
                    'message' => 'This Record Is Deleted',
                ], 410);
            }

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => $post,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error showing post', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
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
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = PostStore::where('id', $id)->first();

            if (! $post) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            if ($post->deleted_at != 0) {
                return response()->json([
                    'status'  => false,
                    'code'    => '410',
                    'message' => 'This Record Is Deleted',
                ], 410);
            }

            $postArray = $post->toArray();
            $dataArray = $postArray['data'];

            foreach ($dataArray as $key => $value) {
                if (is_array($value)) {
                    // If the value is an array, it might be a section
                    foreach ($value as $sectionKey => $sectionValue) {
                        if ($this->isFileName($sectionValue)) {
                            $dataArray[$key][$sectionKey] = URL::to('/uploads/dynamic_post_store/' . $sectionValue);
                        }
                    }
                } else {
                    if ($this->isFileName($value)) {
                        $dataArray[$key] = URL::to('/uploads/dynamic_post_store/' . $value);
                    }
                }
            }

            $postArray['data'] = $dataArray;

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Post Store Data Fetch Successfully',
                'results' => [
                    'data' => $postArray,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /*sdfsdgsggFgD:*
     * Update a post's title and description by its postName.
     * Ensures the post is not deleted before updating. create by ns
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = PostStore::where('id', $id)->where('deleted_at', 0)->first();
            if (! $post) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            $postData = DynamicPost::where('id', $post->post_id)->first();
            if (! $postData) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Data Not Found',
                ], 404);
            }

            $requestData        = $request->all();
            $existingData       = $post->data ?? [];
            $transformedRequest = [];

            foreach ($requestData as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }

                // Handle base64 files (all supported types)
                if (is_string($value) && strpos($value, 'data:') === 0) {
                    preg_match('/data:(.*?);/', $value, $matches);
                    $mimeType = $matches[1] ?? '';
                    $mimeToExt = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/bmp' => 'bmp',
                        'image/svg+xml' => 'svg',
                        'application/pdf' => 'pdf',
                        'application/vnd.ms-excel' => 'xls',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                        'application/msword' => 'doc',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                        'text/plain' => 'txt'
                    ];
                    $extension = $mimeToExt[$mimeType] ?? 'bin';
                    $postname = str_replace(' ', '_', $post->post_name);
                    $fileName = $postname . '_' . $post->id . '_' . $key . '.' . $extension;
                    $transformedRequest[$key] = $fileName;
                    $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                    list($type, $base64Data) = explode(';', $value);
                    list(, $base64Data) = explode(',', $base64Data);
                    $fileData = base64_decode($base64Data);
                    $destinationPath = public_path('uploads/dynamic_post_store');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    file_put_contents($destinationPath . '/' . $fileName, $fileData);
                    continue;
                }

                // Handle section fields
                if (strpos($key, 'Section ') === 0 && (is_array($value) || $this->isJson($value))) {
                    $sectionData = is_array($value) ? $value : json_decode($value, true);
                    $sectionTransformed = [];
                    foreach ($sectionData as $sectionKey => $sectionValue) {
                        if (is_string($sectionValue) && strpos($sectionValue, 'data:') === 0) {
                            preg_match('/data:(.*?);/', $sectionValue, $matches);
                            $mimeType = $matches[1] ?? '';
                            $mimeToExt = [
                                'image/jpeg' => 'jpg',
                                'image/png' => 'png',
                                'image/gif' => 'gif',
                                'image/bmp' => 'bmp',
                                'image/svg+xml' => 'svg',
                                'application/pdf' => 'pdf',
                                'application/vnd.ms-excel' => 'xls',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                                'application/msword' => 'doc',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                                'text/plain' => 'txt'
                            ];
                            $extension = $mimeToExt[$mimeType] ?? 'bin';
                            $postname = str_replace(' ', '_', $post->post_name);
                            $sectionName = str_replace(' ', '_', $key);
                            $fileName = $postname . '_' . $post->id . '_' . $sectionName . '_' . $sectionKey . '.' . $extension;
                            $sectionTransformed[$sectionKey] = $fileName;
                            list($type, $base64Data) = explode(';', $sectionValue);
                            list(, $base64Data) = explode(',', $base64Data);
                            $fileData = base64_decode($base64Data);
                            $destinationPath = public_path('uploads/dynamic_post_store');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0777, true);
                            }
                            file_put_contents($destinationPath . '/' . $fileName, $fileData);
                        } else {
                            $sectionTransformed[$sectionKey] = $sectionValue;
                        }
                        $slugKey = 'Field_Slug_' . $this->convertToSlug($sectionKey);
                        $sectionTransformed[$slugKey] = $this->convertToSlug1($sectionKey);
                    }
                    $transformedRequest[$key] = $sectionTransformed;
                    continue;
                }

                // Handle file uploads (if any)
                if ($value instanceof \Illuminate\Http\UploadedFile && $value->isValid()) {
                    $destinationPath = public_path('uploads/dynamic_post_store');
                    $extension = $value->getClientOriginalExtension();
                    $postname = str_replace(' ', '_', $post->post_name);
                    $fileName = $postname . '_' . $post->id . '_' . $key . '.' . $extension;
                    $value->move($destinationPath, $fileName);
                    $transformedRequest[$key] = $fileName;
                    $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                    continue;
                }

                // Default: keep value as is
                if (is_array($value)) {
                    $transformedRequest[$key] = $value;
                    $slugKey = 'Field_Slug_' . $this->convertToSlug($key);
                    $transformedRequest[$slugKey] = $this->convertToSlug1($key);
                } else {
                    $labelKey = $key;
                    $transformedRequest[$labelKey] = (string) $value;
                    $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                }
            }

            // If value is a URL to your uploads folder, convert to filename
            foreach ($transformedRequest as $key => $value) {
                if (is_string($value) && strpos($value, '/uploads/dynamic_post_store/') !== false) {
                    $transformedRequest[$key] = basename($value);
                }
                // If value is an array (section), check inside it
                if (is_array($value)) {
                    foreach ($value as $sectionKey => $sectionValue) {
                        if (is_string($sectionValue) && strpos($sectionValue, '/uploads/dynamic_post_store/') !== false) {
                            $transformedRequest[$key][$sectionKey] = basename($sectionValue);
                        }
                    }
                }
            }

            // Merge updated values with old ones, but keep old file if new one is not provided
            $finalData = $existingData;

            foreach ($transformedRequest as $key => $value) {
                // If the value is not empty, update it; otherwise, keep the old value
                if ($value !== null && $value !== '') {
                    $finalData[$key] = $value;
                }
                // If the value is an array (section), merge recursively
                if (is_array($value) && isset($existingData[$key]) && is_array($existingData[$key])) {
                    $finalData[$key] = array_replace_recursive($existingData[$key], $value);
                }
            }

            $post->data = $finalData;

            // Transform all file/document fields to URLs for the response
            $dataArray = $finalData;
            foreach ($dataArray as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $sectionKey => $sectionValue) {
                        if ($this->isFileName($sectionValue)) {
                            $dataArray[$key][$sectionKey] = URL::to('/uploads/dynamic_post_store/' . $sectionValue);
                        }
                    }
                } else {
                    if ($this->isFileName($value)) {
                        $dataArray[$key] = URL::to('/uploads/dynamic_post_store/' . $value);
                    }
                }
            }

            if ($post->save()) {
                return response()->json([
                    'status'  => true,
                    'code'    => '200',
                    'message' => 'Post Store Data Updated Successfully',
                    'data'    => $dataArray,
                ], 200);
            } else {
                Log::error('Failed to update post', ['id' => $id]);
                return response()->json([
                    'status'  => false,
                    'code'    => '500',
                    'message' => 'Failed To Update Post Store',
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Error updating post', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    private function convertToSlugBase($string)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $string));
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
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $ids = explode(',', $id);
            $ids = array_filter($ids);

            if (count($ids) > 1) {
                $deletedCount = PostStore::whereIn('id', $ids)->update(['deleted_at' => 1]);

                if ($deletedCount > 0) {
                    return response()->json([
                        'status'        => true,
                        'code'          => '200',
                        'message'       => 'Multi Post Store Data Deleted Successfully',
                        'deleted_count' => $deletedCount,
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => false,
                        'code'    => '404',
                        'message' => 'No Post Store Found To Delete',
                    ], 404);
                }
            } else {
                $post = PostStore::where('id', $ids[0])->first();

                if ($post) {
                    if ($post->deleted_at == 1) {
                        return response()->json([
                            'status'  => false,
                            'code'    => '400',
                            'message' => 'Record Already Deleted',
                        ], 400);
                    }

                    $post->deleted_at = 1;
                    if ($post->save()) {
                        return response()->json([
                            'status'  => true,
                            'code'    => '200',
                            'message' => 'Post Store Data Deleted Successfully',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => false,
                        'code'    => '500',
                        'message' => 'Failed To Delete Post Store',
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'An Error Occurred',
                'error'   => $e->getMessage(),
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
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $idsArray = explode(',', $id);

            // Validation
            $validatedData = Validator::make(
                ['ids' => $idsArray],
                ['ids' => 'required|array|min:1'],
                ['ids.*' => 'integer|exists:post_stores,id']
            );

            if ($validatedData->fails()) {
                return response()->json([
                    'status'  => false,
                    'code'    => '422',
                    'message' => 'Validation Failed',
                    'errors'  => $validatedData->errors(),
                ], 422);
            }

            $posts = postStore::whereIn('id', $idsArray)->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'No Records Found',
                ], 404);
            }

            $newStatus = (int) $request->input('status');

            if ($newStatus !== null && in_array($newStatus, [0, 1])) {
                postStore::whereIn('id', $idsArray)->update(['status' => $newStatus]);

                return response()->json([
                    'status'  => true,
                    'code'    => '200',
                    'message' => 'Post Store Data Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'code'    => '422',
                    'message' => 'Invalid Status Value',
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Error occurred:', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'An Error occurred',
                'error'   => $e->getMessage(),
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
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $post = PostStore::where('id', $id)->first();

            if (! $post) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'Post Store Data Not Found',
                ], 404);
            }

            $data = $post->data;
            $imageDeleted = false;

            // Check if the imageName contains a section (contains a dot)
            if (strpos($imageName, '.') !== false) {
                // Handle section image deletion
                list($sectionName, $imageKey) = explode('.', $imageName);
                
                // Convert section name format (e.g., "Section_1" to "Section 1")
                $sectionName = str_replace('_', ' ', $sectionName);
                
                if (isset($data[$sectionName][$imageKey])) {
                    $imageValue = $data[$sectionName][$imageKey];
                    $data[$sectionName][$imageKey] = null;
                    $imageDeleted = true;
                    
                    // Delete the file from the uploads folder
                    $filePath = public_path('uploads/dynamic_post_store/' . $imageValue);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            } else {
                // Handle regular image deletion
                if (isset($data[$imageName])) {
                    $imageValue = $data[$imageName];
                    $data[$imageName] = null;
                    $imageDeleted = true;
                    
                    // Delete the file from the uploads folder
                    $filePath = public_path('uploads/dynamic_post_store/' . $imageValue);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            if (! $imageDeleted) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'No File Found',
                ], 404);
            }

            $post->data = $data;
            $post->save();

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'File Reference Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting file', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    private function convertToSlug($string)
    {
        return str_replace([' ', '_', '/'], '', strtolower($string));
    }

    private function convertToSlug1($string)
    {
        return str_replace([' ', '/'], '', $string);
    }

    public function addData(Request $request)
    {
        try {

            $inputData  = $request->input('data');
            $parsedData = $this->parseInputData($inputData);

            $validator = Validator::make($parsedData, [
                'field_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'code'   => '422',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $postStore = PostStore::create($parsedData);

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Data added successfully',
                'data'    => $postStore,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error adding data', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function parseInputData($inputData)
    {
        $parsedData = [];

        return $parsedData;
    }

    public function restore($id)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $ids = explode(',', $id);
            $ids = array_filter($ids);

            foreach ($ids as $singlneid) {
                $restoredCount = PostStore::where('id', $singlneid)
                    ->where('deleted_at', 1)
                    ->update(['deleted_at' => 0]);
            }
            return response()->json([
                'status'         => true,
                'code'           => '200',
                'message'        => 'Post Store Data Restored Successfully',
                'restored_count' => $restoredCount,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'An Error Occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all supported file extensions
     * Returns a list of all file extensions supported by the system
     */
    public function getFileExtensions()
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $extensions = [
                'images' => [
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'bmp',
                    'svg'
                ],
                'documents' => [
                    'pdf',
                    'doc',
                    'docx',
                    'xls',
                    'xlsx',
                    'txt'
                ]
            ];

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'File Extensions Retrieved Successfully',
                'data'    => $extensions
            ], 200);
        } catch (Exception $e) {
            Log::error('Error getting file extensions', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a file from the uploads directory
     */
    public function downloadFile($filename)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'code'    => '401',
                    'message' => 'User Not Authenticated',
                ], 401);
            }

            $filePath = public_path('uploads/dynamic_post_store/' . $filename);

            if (!file_exists($filePath)) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'File Not Found',
                ], 404);
            }

            return response()->download($filePath, $filename);
        } catch (Exception $e) {
            Log::error('Error downloading file', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'code'    => '500',
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
