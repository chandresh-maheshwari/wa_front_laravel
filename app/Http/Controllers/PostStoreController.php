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
        if (! is_string($value)) {
            return false;
        }

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
        $extension       = pathinfo($value, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $imageExtensions);
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

            $requestData = $request->all();
            $transformedRequest = [];

            foreach ($requestData as $reqDatakey => $value) {

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
                                    $sectionTransformed[$sectionKey] = $sectionValue;
                                    $slugKey                         = 'Field_Slug_' . $this->convertToSlug($sectionKey);
                                    $sectionTransformed[$slugKey]    = $this->convertToSlug1($sectionKey);
                                }
                                $transformedRequest[$reqDatakey] = $sectionTransformed;
                            }
                        }
                    } elseif (is_array($value)) {
                        $sectionData        = $value;
                        $sectionTransformed = [];
                        foreach ($sectionData as $sectionKey => $sectionValue) {
                            $sectionTransformed[$sectionKey] = $sectionValue;
                            $slugKey                         = 'Field_Slug_' . $this->convertToSlug($sectionKey);
                            $sectionTransformed[$slugKey]    = $this->convertToSlug1($sectionKey);
                        }
                        $transformedRequest[$reqDatakey] = $sectionTransformed;
                    }
                } else {

                    if (strpos($reqDatakey, 'Section_image_') === 0) {
                        continue;
                    }

                    if (is_array($value)) {
                        $transformedRequest[$reqDatakey] = $value;
                        $slugKey                         = 'Field_Slug_' . $this->convertToSlug($reqDatakey);
                        $transformedRequest[$slugKey]    = $this->convertToSlug1($reqDatakey);
                    } else {
                        $labelKey                                                              = $reqDatakey;
                        $transformedRequest[$labelKey]                                         = (string) $value;
                        $transformedRequest['Field_Slug_' . $this->convertToSlug($reqDatakey)] = $this->convertToSlug1($reqDatakey);

                    }
                }
            }

            // Log::info("TESTTTTTTTTTTTTTTTTTTTTTT");
            // Log::info($transformedRequest);
            $postStore = PostStore::create([
                'post_name' => $postTitle,
                'post_id'   => $postData->id,
                'data'      => $transformedRequest,
            ]);

            foreach ($request->all() as $key => $file) {
                if (is_string($file) && strpos($file, 'data:image/') === 0) {

                    list($type, $base64Data) = explode(';', $file);
                    list(, $base64Data)      = explode(',', $base64Data);

                    // Decode the base64 data
                    $imageData = base64_decode($base64Data);

                    preg_match('/data:image\/(.*?);/', $type, $matches);
                    $extension = $matches[1] ?? 'jpg'; // Default to jpg if no extension found

                    if (strpos($key, 'Section_image_') === 0) {
                        continue;
                    }

                    $postname      = str_replace(' ', '_', $postTitle);
                    $fileNameOuter = $postname . '_' . $postStore->id . '_' . $key . '.' . $extension;

                    $destinationPath = public_path('uploads/dynamic_post_store');

                    // Ensure the directory exists; if not, create it
                    if (! file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    // Save the decoded image data to a file
                    file_put_contents($destinationPath . '/' . $fileNameOuter, $imageData);

                    // Handle dynamic fields
                    $labelKey                                                       = $key;
                    $transformedRequest[$labelKey]                                  = $fileNameOuter;
                    $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                }
                // Check if $file is an instance of UploadedFile (regular file upload)
                elseif ($file instanceof \Illuminate\Http\UploadedFile  && $file->isValid()) {
                    // Handle Section-related field names (replace spaces with underscores)
                    if (strpos($key, 'Section ') === 0) {
                        $key = str_replace(' ', '_', $key);
                    }

                    // Skip files with keys starting with Section_image_
                    if (strpos($key, 'Section_image_') === 0) {
                        continue;
                    }

                    // Regular file handling
                    $destinationPath = public_path('uploads/dynamic_post_store');
                    $originalName    = $file->getClientOriginalName();
                    $extension       = $file->getClientOriginalExtension();
                    $postname        = str_replace(' ', '_', $postTitle);
                    $fileNameOuter   = $postname . '_' . $postStore->id . '_' . $key . '.' . $extension;
                    $file->move($destinationPath, $fileNameOuter);

                    // Handle dynamic fields
                    $labelKey                                                       = $key;
                    $transformedRequest[$labelKey]                                  = $fileNameOuter;
                    $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                }
                // In case of an array, loop through the files
                elseif (is_array($file)) {
                    foreach ($file as $individualFile) {
                        // Check if individualFile is an instance of UploadedFile (for file upload)
                        if ($individualFile instanceof \Illuminate\Http\UploadedFile  && $individualFile->isValid()) {
                            $destinationPath = public_path('uploads/dynamic_post_store');
                            $originalName    = $individualFile->getClientOriginalName();
                            $extension       = $individualFile->getClientOriginalExtension();
                            $postname        = str_replace(' ', '_', $postTitle);
                            $fileNameOuter   = $postname . '_' . $postStore->id . '_' . $key . '.' . $extension;
                            $individualFile->move($destinationPath, $fileNameOuter);

                            // Handle dynamic fields
                            $labelKey                                                       = $key;
                            $transformedRequest[$labelKey]                                  = $fileNameOuter;
                            $transformedRequest['Field_Slug_' . $this->convertToSlug($key)] = $this->convertToSlug1($key);
                        }
                    }
                }
            }

            // Save the transformed data back to the PostStore
            $updated = false;
            foreach ($transformedRequest as $sectionKey => $sectionValue) {
                // Check if the value for the section is an array or object (fields inside the section)
                if (is_array($sectionValue) || is_object($sectionValue)) {
                    // Iterate through each key-value pair in the section (fields within the section)
                    foreach ($sectionValue as $fieldKey => $fieldValue) {
                        // Check if the field value is a base64 string (image data)
                        if (is_string($fieldValue) && strpos($fieldValue, 'data:image/') === 0) {
                            // This is base64 data, decode and save it
                            list($type, $base64Data) = explode(';', $fieldValue);
                            list(, $base64Data)      = explode(',', $base64Data); // Get the base64 data part
                            $imageData               = base64_decode($base64Data);

                            // Extract the file extension from the base64 data (e.g., jpeg, png)
                            preg_match('/data:image\/(.*?);/', $type, $matches);
                            $extension = $matches[1] ?? 'jpg'; // Default to jpg if no extension found

                            // Generate the file name for saving the image
                            $fieldnameforimg = str_replace(' ', '_', $fieldKey);
                            $postname        = str_replace(' ', '_', $postTitle);
                            $fileName        = $postname . '_' . $postStore->id . '_' . $sectionKey . '_' . $fieldnameforimg . '.' . $extension;

                            // Define the path where the image will be saved
                            $destinationPath = public_path('uploads/dynamic_post_store');

                            // Ensure the destination directory exists
                            if (! file_exists($destinationPath)) {
                                mkdir($destinationPath, 0777, true);
                            }

                            // Save the decoded image data to the file
                            file_put_contents($destinationPath . '/' . $fileName, $imageData);

                            // Update the transformed request with the file name
                            $transformedRequest[$sectionKey][$fieldKey] = $fileName;
                        }
                    }
                }
            }

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
            Log::error('Error storing post', [
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

            $requestData = $request->all();
            $existingData = $post->data ?? [];
            $transformedRequest = [];

            // Process main level fields (non-nested)
            foreach ($requestData as $key => $value) {
                if (is_numeric($key)) continue;

                if (is_array($value)) {
                    $transformedRequest[$key] = $value;
                } else {
                    $transformedRequest[$key] = $value;
                }
            }

            // Process all fields again to handle specific types like files and base64
            foreach ($requestData as $key => $value) {
                if (is_numeric($key)) continue;

                if (is_array($value)) {
                    // Process nested array (section) fields
                    foreach ($value as $subKey => $subValue) {
                        $originalNestedValue = $request->input($key . '.' . $subKey);

                        if (is_string($originalNestedValue) && strpos($originalNestedValue, 'data:image/') === 0) {
                            // Handle Base64 image within sections
                            list($type, $base64Data) = explode(';', $originalNestedValue);
                            list(, $base64Data) = explode(',', $base64Data);
                            $imageData = base64_decode($base64Data);

                            preg_match('/data:image\/(.*?);/', $type, $matches);
                            $extension = $matches[1] ?? 'jpg';

                            $fieldnameforimg = str_replace(' ', '_', $subKey);
                            $postname = str_replace(' ', '_', $post->post_name);
                            $fileName = $postname . '_' . $post->id . '_' . $key . '_' . $fieldnameforimg . '.' . $extension;

                            $destinationPath = public_path('uploads/dynamic_post_store');
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0777, true);
                            }

                            file_put_contents($destinationPath . '/' . $fileName, $imageData);
                            $transformedRequest[$key][$subKey] = $fileName;
                        } elseif ($originalNestedValue instanceof \Illuminate\Http\UploadedFile && $originalNestedValue->isValid()) {
                            // Handle file upload within sections
                            $destinationPath = public_path('uploads/dynamic_post_store');
                            $extension = $originalNestedValue->getClientOriginalExtension();
                            $postname = str_replace(' ', '_', $post->post_name);
                            $fileName = $postname . '_' . $post->id . '_' . $key . '_' . str_replace(' ', '_', $subKey) . '.' . $extension;
                            $originalNestedValue->move($destinationPath, $fileName);

                            $transformedRequest[$key][$subKey] = $fileName;
                        } elseif (is_string($subValue) && filter_var($subValue, FILTER_VALIDATE_URL)) {
                            // Handle existing URLs within sections
                            if (strpos($subValue, '/uploads/dynamic_post_store/') !== false) {
                                $transformedRequest[$key][$subKey] = basename($subValue);
                            } else {
                                $transformedRequest[$key][$subKey] = $subValue;
                            }
                        } elseif (is_string($subValue)) {
                            // Handle CKEditor content
                            if (strpos($subValue, '<img') !== false) {
                                // Process CKEditor content with images
                                $dom = new \DOMDocument();
                                @$dom->loadHTML(mb_convert_encoding($subValue, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                                $images = $dom->getElementsByTagName('img');
                                
                                foreach ($images as $img) {
                                    $src = $img->getAttribute('src');
                                    if (strpos($src, 'data:image/') === 0) {
                                        // Handle new base64 image
                                        list($type, $base64Data) = explode(';', $src);
                                        list(, $base64Data) = explode(',', $base64Data);
                                        $imageData = base64_decode($base64Data);

                                        preg_match('/data:image\/(.*?);/', $type, $matches);
                                        $extension = $matches[1] ?? 'jpg';

                                        $postname = str_replace(' ', '_', $post->post_name);
                                        $fileName = $postname . '_' . $post->id . '_' . $key . '_' . $subKey . '_' . uniqid() . '.' . $extension;

                                        $destinationPath = public_path('uploads/dynamic_post_store');
                                        if (!file_exists($destinationPath)) {
                                            mkdir($destinationPath, 0777, true);
                                        }

                                        file_put_contents($destinationPath . '/' . $fileName, $imageData);
                                        $img->setAttribute('src', '/uploads/dynamic_post_store/' . $fileName);
                                    } elseif (strpos($src, '/uploads/dynamic_post_store/') !== false) {
                                        // Keep existing image path but store only the filename
                                        $img->setAttribute('src', '/uploads/dynamic_post_store/' . basename($src));
                                    }
                                }
                                $transformedRequest[$key][$subKey] = $dom->saveHTML();
                            } else {
                                $transformedRequest[$key][$subKey] = $subValue;
                            }
                        } else {
                            $transformedRequest[$key][$subKey] = $subValue;
                        }
                    }
                } else {
                    // Process standalone fields
                    if (is_string($value) && strpos($value, 'data:image/') === 0) {
                        // Handle Base64 image for standalone fields
                        list($type, $base64Data) = explode(';', $value);
                        list(, $base64Data) = explode(',', $base64Data);
                        $imageData = base64_decode($base64Data);

                        preg_match('/data:image\/(.*?);/', $type, $matches);
                        $extension = $matches[1] ?? 'jpg';

                        $postname = str_replace(' ', '_', $post->post_name);
                        $fileNameOuter = $postname . '_' . $post->id . '_' . $key . '.' . $extension;
                        $destinationPath = public_path('uploads/dynamic_post_store');

                        if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true);
                        file_put_contents($destinationPath . '/' . $fileNameOuter, $imageData);

                        $transformedRequest[$key] = $fileNameOuter;
                    } elseif ($value instanceof \Illuminate\Http\UploadedFile && $value->isValid()) {
                        // Handle file upload for standalone fields
                        $destinationPath = public_path('uploads/dynamic_post_store');
                        $extension = $value->getClientOriginalExtension();
                        $postname = str_replace(' ', '_', $post->post_name);
                        $fileNameOuter = $postname . '_' . $post->id . '_' . $key . '.' . $extension;
                        $value->move($destinationPath, $fileNameOuter);

                        $transformedRequest[$key] = $fileNameOuter;
                    } elseif (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                        // Handle existing URLs for standalone fields
                        if (strpos($value, '/uploads/dynamic_post_store/') !== false) {
                            $transformedRequest[$key] = basename($value);
                        } else {
                            $transformedRequest[$key] = $value;
                        }
                    } elseif (is_string($value) && strpos($value, '<img') !== false) {
                        // Handle CKEditor content
                        $dom = new \DOMDocument();
                        @$dom->loadHTML(mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        $images = $dom->getElementsByTagName('img');
                        
                        foreach ($images as $img) {
                            $src = $img->getAttribute('src');
                            if (strpos($src, 'data:image/') === 0) {
                                // Handle new base64 image
                                list($type, $base64Data) = explode(';', $src);
                                list(, $base64Data) = explode(',', $base64Data);
                                $imageData = base64_decode($base64Data);

                                preg_match('/data:image\/(.*?);/', $type, $matches);
                                $extension = $matches[1] ?? 'jpg';

                                $postname = str_replace(' ', '_', $post->post_name);
                                $fileName = $postname . '_' . $post->id . '_' . $key . '_' . uniqid() . '.' . $extension;

                                $destinationPath = public_path('uploads/dynamic_post_store');
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0777, true);
                                }

                                file_put_contents($destinationPath . '/' . $fileName, $imageData);
                                $img->setAttribute('src', '/uploads/dynamic_post_store/' . $fileName);
                            } elseif (strpos($src, '/uploads/dynamic_post_store/') !== false) {
                                // Keep existing image path but store only the filename
                                $img->setAttribute('src', '/uploads/dynamic_post_store/' . basename($src));
                            }
                        }
                        $transformedRequest[$key] = $dom->saveHTML();
                    } elseif (empty($value) && isset($existingData[$key]) && $this->isImageFileName($existingData[$key])) {
                        // Preserve existing image if no new value provided
                        $transformedRequest[$key] = $existingData[$key];
                    } else {
                        $transformedRequest[$key] = $value;
                    }
                }
            }

//             // Handle file uploads and base64 images
//            // Handle file uploads and base64 images
// foreach ($request->all() as $key => $file) {
//     if (is_numeric($key)) {
//         continue;
//     }

//     // Initialize $fileName
//     $fileName = null;

//     if (filter_var($file, FILTER_VALIDATE_URL)) {
//         // Skip if it's already a valid URL
//         continue;
//     }

//     // Handle base64 image data
//     if (is_string($file) && strpos($file, 'data:image/') === 0) {
//         list($type, $base64Data) = explode(';', $file);
//         list(, $base64Data)      = explode(',', $base64Data);
//         $imageData               = base64_decode($base64Data);

//         preg_match('/data:image\/(.*?);/', $type, $matches);
//         $extension = $matches[1] ?? 'jpg';

//         // Handle filename and directory
//         $postname        = str_replace(' ', '_', $post->post_name);
//         $fileNameOuter   = $postname . '_' . $post->id . '_' . $key . '.' . $extension;
//         $destinationPath = public_path('uploads/dynamic_post_store');

//         if (! file_exists($destinationPath)) {
//             mkdir($destinationPath, 0777, true);
//         }

//         file_put_contents($destinationPath . '/' . $fileNameOuter, $imageData);
//         $fileName = $fileNameOuter; // Ensure $fileName is set
//     }

//     // Handle regular file uploads
//     elseif ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
//         $destinationPath = public_path('uploads/dynamic_post_store');
//         $extension = $file->getClientOriginalExtension();
//         $postname        = str_replace(' ', '_', $post->post_name);
//         $fileNameOuter   = $postname . '_' . $post->id . '_' . $key . '.' . $extension;

//         $file->move($destinationPath, $fileNameOuter);
//         $fileName = $fileNameOuter; // Ensure $fileName is set
//     }

//     // Handle file arrays
//     elseif (is_array($file)) {
//         foreach ($file as $individualFile) {
//             if ($individualFile instanceof \Illuminate\Http\UploadedFile && $individualFile->isValid()) {
//                 $destinationPath = public_path('uploads/dynamic_post_store');
//                 $extension = $individualFile->getClientOriginalExtension();
//                 $postname        = str_replace(' ', '_', $post->post_name);
//                 $fileNameOuter   = $postname . '_' . $post->id . '_' . $key . '.' . $extension;

//                 $individualFile->move($destinationPath, $fileNameOuter);
//                 $fileName = basename($fileNameOuter); // Ensure $fileName is set
//             }
//         }
//     } else {
//         // If the file is a URL or non-file field, just use the original value
//         if (! empty($file)) {
//             $extension = pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION);
//             $fileName  = ! empty($extension) ? basename($file) : $file;
//         }
//     }

//     // Now set the $transformedRequest with the file name
//     if ($fileName) {
//         $transformedRequest[$key] = $fileName;
//     }
// }


//             // Handle nested image fields inside sections
//             foreach ($transformedRequest as $sectionKey => $sectionValue) {
//                 if (is_array($sectionValue)) {
//                     foreach ($sectionValue as $fieldKey => $fieldValue) {
//                         if (filter_var($fieldValue, FILTER_VALIDATE_URL)) {
//                             // Check if the URL is already a complete path to our upload directory
//                             if (strpos($fieldValue, '/uploads/dynamic_post_store/') !== false) {
//                                 // Extract just the filename from the full URL
//                                 $transformedRequest[$sectionKey][$fieldKey] = basename($fieldValue);
//                             } else {
//                                 $transformedRequest[$sectionKey][$fieldKey] = $fieldValue;
//                             }
//                         } elseif (is_string($fieldValue) && strpos($fieldValue, 'data:image/') === 0) {
//                             list($type, $base64Data) = explode(';', $fieldValue);
//                             list(, $base64Data)      = explode(',', $base64Data);
//                             $imageData               = base64_decode($base64Data);

//                             preg_match('/data:image\/(.*?);/', $type, $matches);
//                             $extension = $matches[1] ?? 'jpg';

//                             $fieldnameforimg = str_replace(' ', '_', $fieldKey);
//                             $postname        = str_replace(' ', '_', $post->post_name);
//                             $fileName        = $postname . '_' . $post->id . '_' . $sectionKey . '_' . $fieldnameforimg . '.' . $extension;

//                             $destinationPath = public_path('uploads/dynamic_post_store');
//                             if (! file_exists($destinationPath)) {
//                                 mkdir($destinationPath, 0777, true);
//                             }

//                             file_put_contents($destinationPath . '/' . $fileName, $imageData);
//                             $transformedRequest[$sectionKey][$fieldKey] = $fileName;
//                         } elseif (is_string($fieldValue)) {
//                             if (! empty($fieldValue)) {
//                                 $extension = pathinfo(parse_url($fieldValue, PHP_URL_PATH), PATHINFO_EXTENSION);
//                                 $fileName  = ! empty($extension) ? basename($fieldValue) : $fieldValue;
//                             }
//                             $transformedRequest[$sectionKey][$fieldKey] = basename($fieldValue);
//                         }
//                     }
//                 }
//             }

//             // Merge updated values with old ones
            $finalData = array_replace_recursive($existingData, $transformedRequest);
            
            // Ensure we don't lose any existing image references and prevent duplicate paths
            foreach ($existingData as $key => $value) {
                if (is_array($value)) {
                    // Handle nested arrays (sections)
                    foreach ($value as $subKey => $subValue) {
                        if ($this->isImageFileName($subValue)) {
                            if (!isset($finalData[$key][$subKey]) || empty($finalData[$key][$subKey])) {
                                $finalData[$key][$subKey] = $subValue;
                            } elseif (is_string($finalData[$key][$subKey]) && strpos($finalData[$key][$subKey], '/uploads/dynamic_post_store/') !== false) {
                                // If the value contains a full path, convert it to just the filename
                                $finalData[$key][$subKey] = basename($finalData[$key][$subKey]);
                            }
                        }
                    }
                } elseif ($this->isImageFileName($value)) {
                    if (!isset($finalData[$key]) || empty($finalData[$key])) {
                        $finalData[$key] = $value;
                    } elseif (is_string($finalData[$key]) && strpos($finalData[$key], '/uploads/dynamic_post_store/') !== false) {
                        // If the value contains a full path, convert it to just the filename
                        $finalData[$key] = basename($finalData[$key]);
                    }
                }
            }

            Log::info('Final Data:', $finalData);
            $post->data = $finalData;

            if ($post->save()) {
                return response()->json([
                    'status'  => true,
                    'code'    => '200',
                    'message' => 'Post Store Data Updated Successfully',
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

            $data         = $post->data;
            $imageDeleted = false;

            $normalizedImageName      = trim($imageName);
            $normalizedImageNameLower = strtolower($normalizedImageName);

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $normalizedSubValue = strtolower(trim($subValue));
                        if ($normalizedSubValue === $normalizedImageNameLower) {
                            $data[$key][$subKey] = "";
                            $imageDeleted        = true;
                            break 2;
                        }
                    }
                } else {
                    $normalizedValue = strtolower(trim($value));
                    if ($normalizedValue === $normalizedImageNameLower) {
                        $data[$key]   = "";
                        $imageDeleted = true;
                        break;
                    }
                }
            }

            if (! $imageDeleted) {
                return response()->json([
                    'status'  => false,
                    'code'    => '404',
                    'message' => 'No Image Found',
                ], 404);
            }

            $post->data = $data;
            $post->save();

            return response()->json([
                'status'  => true,
                'code'    => '200',
                'message' => 'Image Reference Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting image', ['error' => $e->getMessage()]);
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
}
