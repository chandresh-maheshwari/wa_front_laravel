<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Page;
use App\Models\PostStore;
use App\Models\DynamicPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $pages = Page::orderBy('id', 'desc')->get();

        if ($pages->isEmpty()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'No Page Data Found',
                'results' => [],
            ], 200);
        }

        $pages->transform(function ($page) {
            $page->image_url = $page->image ? url('/uploads/page/' . $page->image) : null;
            return $page;
        });

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $pages,
        ], 200);
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $this->validate($request, [
            'page_name' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,gif,svg|dimensions:max_width=1600,max_height=1600|dimensions:min_width=40,min_height=40',
            'ordering' => 'nullable|integer', // Ensure ordering is an integer
        ], [

            'image.file' => 'The image must be a valid file.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, gif, svg.',
            'image.dimensions' => 'The image must have valid dimensions (max: 1600x1600, min: 40x40).',
        ]);

        if (Page::where('ordering', $request->ordering)->exists()) {
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => 'The ordering value already exists. Please choose a different one',
            ], 400);
        }
        $page = new Page();
        $page->post_type = $request->post_type;
        $page->page_name = $request->page_name;
        $page->page_description = $request['page_description'];
        $page->button_name = $request->button_name;
        $page->button_link = $request->button_link;

        if ($page->save()) {
            $pageId = $page->id;
            if ($request->hasFile('image')) {
                $extension = $request->image->getClientOriginalExtension();
                $imageName = $pageId . '_' . strtolower(str_replace(' ', '_', $request->page_name)) . '._.' . $extension;

                $request->image->move(public_path('/uploads/page'), $imageName);

                $page->image = $imageName;
                $page->save();
            } else {
                $page->image = null;
            }

            $page->ordering = $request['ordering'];
            $page->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

            

            if ($page->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Page Data Added Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Something Went Wrong'
                ], 404);
            }
        }
    }


    public function show($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $page = page::where('id', $id)->where('deleted_at', 0)->first();
        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $page,
        ], 200);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $page = Page::where('deleted_at', 0)->find($id);
        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        $postTitle = DynamicPost::where('id', $page->post_type)->value('post_title');
        if ($page->image) {
            $page->image = url('/uploads/page/' . $page->image);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $page,
            'post_title' => $postTitle,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $page = Page::find($id);
        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        // $this->validate($request, [
        //     'page_name' => 'nullable|string', 
        //     'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg|dimensions:max_width=1600,max_height=1600|dimensions:min_width=40,min_height=40',
        // ]);

        if ($request->post_type) {
            $page->post_type = $request->post_type;
        }
        if ($request->page_name) {
            $page->page_name = $request->page_name;
        }
        if ($request->page_description) {
            $page->page_description = $request->page_description;
        }
        if ($request->button_name) {
            $page->button_name = $request->button_name;
        }
        if ($request->button_link) {
            $page->button_link = $request->button_link;
        }

        if ($request->hasFile('image')) {
            $pageImage = $request->image->getClientOriginalName();

            $request->image->move(public_path('/uploads/page'), $pageImage);

            $page->image = $pageImage;
        }
        if ($request->ordering) {
            $page->ordering = $request->ordering;
        }
        if ($request->deleted_at) {
            $page->deleted_at = $request->deleted_at;
        }

        // Save the updated page data
        if ($page->save()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Page Data Updated Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Something Went Wrong'
            ], 500);
        }
    }

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

            $posts = Page::whereIn('id', $idsArray)->get();

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
                'message' => 'Page Data Updated Successfully',
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
                $deletedCount = Page::whereIn('id', $ids)->update(['deleted_at' => 1]);

                if ($deletedCount > 0) {
                    return response()->json([
                        'status' => true,
                        'code' => '200',
                        'message' => 'Page Data Deleted Successfully',
                        'deleted_count' => $deletedCount,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '404',
                        'message' => 'No Page Found To Delete',
                    ], 404);
                }
            } else {
                $post = Page::where('id', $ids[0])->first();

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
                            'message' => 'Page Data Deleted Successfully',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => '500',
                        'message' => 'Failed To Delete Page Data',
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

    /** This function used for the post show by page name create by ns */

    public function showByPageName($pageName)
    {
        try {
            $page = Page::where('page_name', $pageName)
                ->where('deleted_at', 0)
                ->where('status', 1)
                ->first();

            if (!$page) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Page Data Not Found',
                ], 404);
            }

            if (!empty($page->image)) {
                $page->image = url('uploads/page/' . $page->image);
            }

            $postStores = PostStore::where('post_id', $page->post_type)
                ->where('status', 1)
                ->where('deleted_at', 0)
                ->get();
           

            if ($postStores->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Related Post Store Data Not 
                     ',
                ], 404);
            }

            $allRestructuredData = [];

            foreach ($postStores as $postStore) {
                $postStoreData = $postStore->toArray();

                // Transform keys recursively
                $postStoreData['data'] = $this->transformKeys($postStoreData['data']);

                $allRestructuredData[] = $postStoreData;
            }
           
            $pageData = $page->toArray();
            $pageData['post_store'] = $allRestructuredData;

            $slugKey = str_replace('-', '_', $page->slug);
            $pageData['slug'] = $slugKey;

            return response()->json([
                'status' => true, 
                
                'code' => '200',
                'message' => 'Page And Post Store Data Fetch Successfully',
                'page' => $pageData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /** Get data page with him post store by ordering by ns */

    public function showAllPagesWithPostStores()
    {
        try {
            $pages = Page::whereNotNull('ordering')
                ->where('deleted_at', 0)
                ->orderBy('ordering', 'asc')
                ->get();

            if ($pages->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Page Data Found',
                ], 404);
            }

            $allPagesData = [];

            foreach ($pages as $page) {
                if (!empty($page->image)) {
                    $page->image = url('uploads/page/' . $page->image);
                }

                $postStores = PostStore::where('post_id', $page->post_type)
                                         ->where('status', 1)
                                         ->where('deleted_at', 0)
                                         ->get();

               

                $allRestructuredData = [];

                foreach ($postStores as $postStore) {
                    $postStoreData = $postStore->toArray();

                    Log::info("WWWWWWWWWWWWWWWWW");
                    Log::info($postStoreData);
                    // Transform keys recursively
                    $postStoreData = $this->transformKeys($postStoreData);

                    $allRestructuredData[] = $postStoreData;
                }
               

                $pageData = $page->toArray();
                $pageData['post_store'] = $allRestructuredData;

                $slugKey = str_replace('-', '_', $page->slug);
                $pageData['slug'] = $slugKey;

                $allPagesData[$slugKey] = $pageData;
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'All Pages And Post Store Data Fetch Successfully',
                'results' => $allPagesData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    // Helper function to transform keys
    private function transformKeys(array $data)
    {
        $transformedData = [];
        $fileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'doc', 'docx'];

        foreach ($data as $key => $value) {
            // $normalizedKey = ucfirst(strtolower(preg_replace('/\s+/', '', $key)));
            $normalizedKey = preg_replace('/\s+/', '', $key);
            Log::info("AAAAAAA");
            Log::info($normalizedKey);

            if (is_array($value)) {
                $transformedData[$normalizedKey] = $this->transformKeys($value);
            } else {
                if (!empty($value) && is_string($value)) {
                    // $fileExtension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                    $fileExtension = pathinfo($value, PATHINFO_EXTENSION);  

                    if (in_array($fileExtension, $fileExtensions)) {
                        $transformedData[$normalizedKey] = url('/uploads/dynamic_post_store/' . $value);
                    } else {
                        $transformedData[$normalizedKey] = $value;
                    }
                } else {
                    $transformedData[$normalizedKey] = $value;
                }
            }
        }

        return $transformedData;
    }


    // private function transformKeys(array $data)
    // {
    //     $transformedData = [];
    //     $fileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'doc', 'docx'];

    //     foreach ($data as $key => $value) {
    //         // Remove spaces from the key
    //         $keyNoSpaces = preg_replace('/\s+/', '', $key);

    //         // Check conditions
    //         $shouldUcFirst = is_array($value) || is_object($value) || strpos($keyNoSpaces, 'Field_Slug_') === 0;

    //         // Apply ucfirst if needed
    //         $normalizedKey = $shouldUcFirst ? ucfirst($keyNoSpaces) : $keyNoSpaces;

    //         Log::info("Normalized Key: " . $normalizedKey);

    //         // Recursively transform if it's an array
    //         if (is_array($value)) {
    //             $transformedData[$normalizedKey] = $this->transformKeys($value);
    //         } else {
    //             if (!empty($value) && is_string($value)) {
    //                 $fileExtension = strtolower(pathinfo($value, PATHINFO_EXTENSION));

    //                 if (in_array($fileExtension, $fileExtensions)) {
    //                     $transformedData[$normalizedKey] = url('/uploads/dynamic_post_store/' . $value);
    //                 } else {
    //                     $transformedData[$normalizedKey] = $value;
    //                 }
    //             } else {
    //                 $transformedData[$normalizedKey] = $value;
    //             }
    //         }
    //     }

    //     return $transformedData;
    // }




    /** This function used for the page status active or inactive create by ns  */

    public function pageStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $statusData = Page::find($id);

        if (!$statusData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record Not Found',
            ], 404);
        }

        $newStatus = $request->input('page_status');

        if ($newStatus !== null && in_array($newStatus, [0, 1])) {

            $statusData->page_status = $newStatus;
            $statusData->save();

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Inner Page Data Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => '422',
                'message' => 'Invalid Status Value',
            ], 422);
        }
    }

    /** This function used for the if status is active that page name get only create by ns */
    public function getActivePageData()
    {

        $page = Page::select('page_name')->where('page_status', 1)->get();

        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Active Page Not Found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Active Page Data Retrieved Successfully',
            'data' => $page
        ]);
    }

    public function deletePageImage($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User Not Authenticated',
            ], 401);
        }

        $page = Page::find($id);
        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Not Found',
            ], 404);
        }

        if ($page->image) {
            $page->image = null;
            $page->save();

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Image Deleted Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'No Image Found',
            ], 404);
        }
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

            $restoredCount = Page::whereIn('id', $ids)->where('deleted_at', 1)->update(['deleted_at' => 0]);

            if ($restoredCount > 0) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Page Data Restored Successfully',
                    'restored_count' => $restoredCount,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'No Page Found To Restore',
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
