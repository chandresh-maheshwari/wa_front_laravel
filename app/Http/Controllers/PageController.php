<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $page = Page::where('deleted_at', 0)->get();

        if ($page->isEmpty()) {
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
            'page_name' => 'required',
            'page_description' => 'required',
            'image' => 'required',
            'ordering' => 'required'
        ]);

        $page = new Page();
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
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $data = Page::where('deleted_at', 0)->find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Page Data Not Found',
            ], 404);
        }
        $data->image_url = $data->image ? url('/images/page/' . $data->image) : null;

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Page Data Fetch Successfully',
            'results' => $data,
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

        $page->post_id = $request->post_id;
        $page->title = $request['title'];
        $page->description = $request['description'];

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

        $status = Page::find($id);

        if (!$status) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Record not found',
            ], 404);
        }

        $status->active = $status->active ? 0 : 1;
        $status->save();

        $message = $status->active ? 'Activated Successfully' : 'Deactivated Successfully';

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => $message,
            'data' => $status->active
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $deletePage = Page::find($id);

        if ($deletePage) {
            $deletePage->deleted_at = 1;
            if ($deletePage->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Page Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Page Found For Deletion',
        ], 404);
    }
}
