<?php

namespace App\Http\Controllers;

use App\Models\ChooseWasteAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChooseWasteAccountController extends Controller
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
        $chooseWasteAccountant = ChooseWasteAccount::where('deleted_at', 0)->get();
        if ($chooseWasteAccountant->isEmpty()) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Choose Waste Accountant Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Choose Waste Accountant Data Fetch Successfully',
            'results' => $chooseWasteAccountant,
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
            'name' => 'required',
            'account_title' => 'required',
            'account_description' => 'required'
        ]);

        $chooseWasteAccountant = new ChooseWasteAccount();
        $chooseWasteAccountant->name = $request['name'];
        $chooseWasteAccountant->account_title = $request['account_title'];
        $chooseWasteAccountant->account_description = $request['account_description'];
        $chooseWasteAccountant->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($chooseWasteAccountant->save() == true) {
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Choose Waste Accountant Added Successfully',
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
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }

        $chooseWasteAccountant = ChooseWasteAccount::where('id', $id)->where('deleted_at', 0)->first();
        if (!$chooseWasteAccountant) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Choose Waste Accountant Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Choose Waste Accountant Data Fetch Successfully',
            'results' => $chooseWasteAccountant,
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
        $chooseWasteAccountant = ChooseWasteAccount::where('deleted_at', 0)->find($id);

        if (!$chooseWasteAccountant) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Choose Waste Accountant Data Not Found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Choose Waste Accountant Data Fetch Successfully',
            'results' => $chooseWasteAccountant,
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

        $chooseWasteAccountant = ChooseWasteAccount::find($id);
        if (!$chooseWasteAccountant) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'Choose Waste Accountant Data Not Found',
            ], 404);
        }
        $chooseWasteAccountant->name = $request->name;
        $chooseWasteAccountant->account_title = $request->account_title;
        $chooseWasteAccountant->account_description = $request->account_description;
        $chooseWasteAccountant->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        $chooseWasteAccountant->update();

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Choose Waste Accountant Updated Successfully',
        ], 200);
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
        $deleteAccountant = ChooseWasteAccount::find($id);

        if ($deleteAccountant) {
            $deleteAccountant->deleted_at = 1;
            if ($deleteAccountant->save()) {
                return response()->json([
                    'status' => true,
                    'code' => '200',
                    'message' => 'Choose Waste Accountant Data Deleted Successfully',

                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'code' => '404',
            'message' => 'No Matching Choose Waste Accountant Found For Deletion',
        ], 404);
    }

    public function active($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'User not authenticated',
            ], 401);
        }
        $status = ChooseWasteAccount::find($id);
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
}
