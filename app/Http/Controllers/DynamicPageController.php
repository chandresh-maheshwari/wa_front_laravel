<?php

namespace App\Http\Controllers;

use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DynamicPageController extends Controller
{
    protected $request;
    protected $dynamicPage1;
    function __construct(Request $request, DynamicPage $dynamicPage)
    {
        $this->request = $request;
        $this->dynamicPage1 = $dynamicPage;
    }

    /** 
     * List all pages that are not deleted.
     * Ensures the user is authenticated before fetching the pages. create by ns
     */

     public function listPages()
     {
         try {
             $user = Auth::user()->id;
             if (!$user) {
                 return response()->json([
                     'status' => false,
                     'code' => '401',
                     'message' => 'User not authenticated',
                 ], 401);
             }
         
             $page = DynamicPage::where('deleted_at', 0)->get();
         
             if ($page->isEmpty()) {
                 return response()->json([
                     'status' => true,
                     'code' => '200',
                     'message' => 'No Page Data Found',
                     'results' => [], 
                 ], 200);
             }
         
             return response()->json([
                 'status' => true,
                 'code' => '200',
                 'message' => 'Page Data Fetch Successfully',
                 'results' => $page,
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => false,
                 'code' => '500',
                 'message' => 'An error occurred: ' . $e->getMessage(),
             ], 500);
         }
     }

    /** 
     * Store a new page in the database.
     * Validates the request data before saving. create by ns
     */

    public function addPage(Request $request)
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

            $this->validate($request, [
                'page_name' => 'required|string|max:255',
                'page_data' => 'required',
                'page_data.*.label' => 'required|string',
                'page_data.*.type' => 'required|string',
            ]);

            $pageData = $request['page_data'];
            $ordering = $request['ordering'] ?? 1;
            if ($ordering == 0) {
                $ordering = 1;
            }

            $saveData = $this->dynamicPage1->savePage([
                'page_name' => $request['page_name'],
                'page_data' => $pageData,
                'ordering' => $ordering
            ]);

            if (isset($saveData) && $saveData !== false) {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Display a specific page by its ID.
     * Ensures the page is not deleted before displaying. create by ns
     */

    public function show($id)
    {
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $page = DynamicPage::where('id', $id)->where('deleted_at', 0)->first();
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Retrieve a page by its ID for editing.
     * Ensures the page is not deleted before fetching. create by ns
     */

    public function edit($id)
    {
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => '401',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $data = DynamicPage::where('deleted_at', 0)->find($id);
            if (!$data) {
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
                'results' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Update a page's name
     * Ensures the page is not deleted before updating. create by ns
     */

    public function update(Request $request, $id)
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

            $page = DynamicPage::where('id', $id)->where('deleted_at', 0)->first();
            if (!$page) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Page Data Not Found',
                ], 404);
            }

            $page->page_name = $request['page_name'];

            if ($request->has('page_data')) {
                $page->page_data = $request['page_data'];
            }

            if ($request->has('ordering')) {
                $page->ordering = $request['ordering'];
            }
            if ($page->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Page Updated Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'code' => '500',
                    'message' => 'Failed To Update Page',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Soft delete a page by its title.
     * If the page is already deleted, it returns a message indicating so. create by ns
     */

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

            $page = DynamicPage::where('id', $id)->first();

            if ($page) {
                if ($page->deleted_at == 1) {
                    return response()->json([
                        'status' => false,
                        'code' => '400',
                        'message' => 'Record Already Deleted',
                    ], 400);
                }

                $page->deleted_at = 1;
                if ($page->save()) {
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
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Toggle the active status of a page by its title.
     * If the page is active, it will be deactivated. create by ns
     */
    public function active($id)
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

            $data = DynamicPage::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Record not found',
                ], 404);
            }

            $data->status = $data->status ? 0 : 1;
            $data->save();

            $message = $data->status ? 'Activated Successfully' : 'Deactivated Successfully';

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => $message,
                'data' => $data->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}

