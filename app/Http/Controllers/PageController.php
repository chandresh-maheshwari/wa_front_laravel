<?php

namespace App\Http\Controllers;

use App\Models\DynamicPost;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $pages = Page::where('deleted_at', 0)->orderBy('id', 'desc')->get();

        if ($pages->isEmpty()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'No Post Data Found',
                'results' => [],
            ], 200);
        }

        $pages->transform(function ($page) {
            $page->image_url = $page->image ? url('/images/page/' . $page->image) : null;
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
                'message' => 'User not authenticated',
            ], 401);
        }

        $this->validate($request, [
            'post_type' => 'required',
            'page_name' => 'required',
            'page_description' => 'required',
            'image' => 'required',
            'ordering' => 'required'
        ]);

        $page = new Page();
        $page->post_type = $request->post_type;
        $page->page_name = $request->page_name;
        $page->page_description = $request['page_description'];

        if ($request->hasFile('image')) {
            $pageImage = $request->image->getClientOriginalName();
            $request->image->move(public_path('/images/page'), $pageImage);
            $page->image = $pageImage;
        } else {
            $page->image = null;
        }

        $page->ordering = $request['ordering'] == 0 ? 1 : $request['ordering'];
        $page->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($page->save()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Page Added Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Something went wrong'
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
                'message' => 'User not authenticated',
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
                'message' => 'User not authenticated',
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

        $page->image_url = $page->image ? url('/images/page/' . $page->image) : null;

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
                'message' => 'User not authenticated',
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
        $page->post_type = $request->post_type;
        $page->page_name = $request->page_name;
        $page->page_description = $request['page_description'];

        if ($request->hasFile('image')) {
            $pageImage = $request->image->getClientOriginalName();
            $request->image->move(public_path('/images/page'), $pageImage);
            $page->image = $pageImage;
        }

        $page->ordering = $request['ordering'] == 0 ? 1 : $request['ordering'];
        $page->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($page->save()) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Page Updated Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    public function active($id)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $statusData = Page::find($id);

        if (!$statusData) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record not found',
            ], 404);
        }

        $statusData->status = $statusData->status ? 0 : 1;
        $statusData->save();

        $message = $statusData->status ? 'Activated Successfully' : 'Deactivated Successfully';

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => $message,
            'data' => $statusData->status
        ]);
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $post = Page::where('id', $id)->first();

            if ($post) {
                if ($post->deleted_at == 1) {
                    return response()->json([
                        'status' => false,
                        'code' => '400',
                        'message' => 'Record already deleted',
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
                    'message' => 'Failed To Delete Page',
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
}
