<?php

namespace App\Http\Controllers;

use App\Models\ProducerReceiver;
use Illuminate\Http\Request;

class ProducerReceiverController extends Controller
{

    public function index()
    {
        $producerReceiverData = ProducerReceiver::where('deleted_at', 0)->get();
        if ($producerReceiverData->isNotEmpty()) {
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'Producer & Receiver Data Fetch Successfully',
                'results' => $producerReceiverData,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Producer & Receiver Data Not Found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'section_title' => 'required',
            'section_image' => 'required|image|mimes:jpeg,png,jpg',
            'producer_title' => 'required',
            'producer_description' => 'required',
            'receiver_title' => 'required',
            'receiver_description' => 'required'

        ]);

        $sectionImage = $request->section_image->getClientOriginalName();
        $request->section_image->move(public_path('/images/sectionProducerReceiver'), $sectionImage);

        $producerReceiver = new ProducerReceiver();
        $producerReceiver->section_image = $sectionImage;
        $producerReceiver->section_title = $request['section_title'];
        $producerReceiver->producer_title = $request['producer_title'];
        $producerReceiver->producer_description = $request['producer_description'];
        $producerReceiver->receiver_title = $request['receiver_title'];
        $producerReceiver->receiver_description = $request['receiver_description'];
        $producerReceiver->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;

        if ($producerReceiver->save() == true) {
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'Producer & Receiver Added Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    public function show($id)
    {
        $producerReceiver = ProducerReceiver::where('id', $id)->where('deleted_at', 0)->first();
        if (!$producerReceiver) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Producer & Receiver Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Producer & Receiver Data Fetch Successfully',
            'results' => $producerReceiver,
        ], 200);
    }

    public function edit($id)
    {
        $data = ProducerReceiver::where('deleted_at', 0)->findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Producer & Receiver Data Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Producer & Receiver Data Fetch Successfully',
            'results' => $data,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $producerReceiver = ProducerReceiver::find($request->id);
        if ($request->hasFile('section_image')) {
            $file1 = $request->file('section_image');
            $originalName = $file1->getClientOriginalName();
            $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
            $file1->move(public_path('/images/sectionProducerReceiver'), $imageName1);
            $producerReceiver->section_image = url('/images/sectionProducerReceiver/' . $imageName1);
            $producerReceiver->section_image = url('/images/sectionProducerReceiver/' . $originalName);
        }

        $producerReceiver->section_title = $request->section_title;
        $producerReceiver->producer_title = $request->producer_title;
        $producerReceiver->producer_description = $request->producer_description;
        $producerReceiver->receiver_title = $request->receiver_title;
        $producerReceiver->receiver_description = $request->receiver_description;
        $producerReceiver->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
       
        if (!$producerReceiver) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Producer & Receiver Data Not Found',

            ], 404);
        }
        
        $producerReceiver->update();
        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Producer & Receiver Data Updated Successfully',
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $deleteData = ProducerReceiver::find($request->id);
        if ($deleteData) {
            $deleteData->deleted_at = 1;
            if ($deleteData->save()) {
                return response()->json([
                    'status' => 'Success',
                    'code' => '200',
                    'message' => 'Producer & Receiver Data Deleted Successfully',

                ],200);
            }
        }
        return response()->json([
            'status' => 'Error',
            'code' => '404',
            'message' => 'No Matching Producer & Receiver Found For Deletion',
        ],404);
    }
    }

