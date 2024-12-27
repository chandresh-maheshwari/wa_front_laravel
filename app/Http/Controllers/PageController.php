<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\PostStore;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

        $pages = Page::where('deleted_at', 0)->orderBy('id', 'desc')->get();

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
            'page_name' => 'required',
        ]);
        $page = new Page();
        $page->post_type = $request->post_type;
        $page->page_name = $request->page_name;
        $page->page_description = $request['page_description'];

        if ($request->hasFile('image')) {
            $pageImage = $request->image->getClientOriginalName();
            $request->image->move(public_path('/uploads/page'), $pageImage);
            $imageUrl = url('uploads/page/' . $pageImage);
            $page->image = $imageUrl;
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

        $page->image_url = $page->image ? url('/uploads/page/' . $page->image) : null;

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

        if ($request->has('post_type')) {
            $page->post_type = $request->post_type;
        }
        if ($request->has('page_name')) {
            $page->page_name = $request->page_name;
        }
        if ($request->has('page_description')) {
            $page->page_description = $request->page_description;
        }

        if ($request->hasFile('image')) {
            $pageImage = $request->image->getClientOriginalName();

            $imagePath = $request->image->move(public_path('/uploads/page'), $pageImage);

            $imageUrl = url('uploads/page/' . $pageImage);
            $page->image = $imageUrl;
        }

        if ($request->has('ordering')) {
            $page->ordering = $request->ordering;
        }
        if ($request->has('deleted_at')) {
            $page->deleted_at = $request->deleted_at;
        }

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
        $page = Page::where('page_name', $pageName)->where('deleted_at', 0)->where('status', 1)->first();
        if (!$page) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }

        $page->slug = str_replace('-', '_', $page->slug);

        $postStores = PostStore::where('post_id', $page->post_type)
            ->where('status', 1)
            ->get();
        if ($postStores->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Related Post Store Data Not Found',
            ], 404);
        }

        $allRestructuredData = [];

        foreach ($postStores as $postStore) {
            $restructuredData = [];
            $data = $postStore->data;

            foreach ($data as $key => $value) {
                if (strpos($key, 'field_slug_') === 0) {
                    continue;
                }

                $slugKey = 'field_slug_' . str_replace(' ', '', strtolower($key));
                if (isset($data[$slugKey])) {
                    $slug = $data[$slugKey];
                    $restructuredData[$slug] = $value;
                }
            }

            $postStoreResponse = $postStore->toArray();
            $postStoreResponse['data'] = $restructuredData;
            $allRestructuredData[] = $postStoreResponse;
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page And Post Store Data Fetch Successfully',
            'page' => $page,
            'post_store' => $allRestructuredData,
        ], 200);
    }

    /** Get data page with him post store by ordering by ns */

    public function showAllPagesWithPostStores()
    {
        $pages = Page::whereNotNull('ordering')
            ->where('deleted_at', 0)
            // ->where('status', 1)
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
            $postStores = PostStore::where('post_id', $page->post_type)
                ->where('status', 1)
                ->get();

            $allRestructuredData = [];

            foreach ($postStores as $postStore) {
                $restructuredData = [];
                $data = $postStore->data;

                foreach ($data as $key => $value) {
                    if (strpos($key, 'field_slug_') === 0) {
                        continue;
                    }

                    $slugKey = 'field_slug_' . str_replace(' ', '', strtolower($key));
                    if (isset($data[$slugKey])) {
                        $slug = $data[$slugKey];
                        $restructuredData[$slug] = $value;
                    }
                }

                $capitalizedData = [];
                foreach ($restructuredData as $key => $value) {
                    $capitalizedKey = ucfirst($key);
                    $capitalizedData[$capitalizedKey] = $value;
                }

                $postStoreResponse = $postStore->toArray();
                $postStoreResponse = array_merge($postStoreResponse, $capitalizedData);
                unset($postStoreResponse['data']);
                $allRestructuredData[] = $postStoreResponse;
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
    }

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
}
