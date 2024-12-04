<?php

namespace App\Http\Controllers;

use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetPageFormDataController extends Controller
{
    public function getpageData($pageName)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        try {
            $pageData = DynamicPage::where('page_name', $pageName)->first();

            if (!$pageData) {
                return response()->json([
                    'status' => false,
                    'code' => '404',
                    'message' => 'Page not found',
                ], 404);
            }

            if ($pageData->deleted_at != 0) {
                return response()->json([
                    'status' => false,
                    'code' => '410',
                    'message' => 'This Record is deleted',
                ], 410);
            }

            return response()->json([
                'status' => true,
                'code' => '200',
                'data' => $pageData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'An error occurred while fetching the page data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
