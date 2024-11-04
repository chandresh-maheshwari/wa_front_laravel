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
                'status' => 1,
                'message' => 'Producer & Receiver added successfully',
                'results' => $producerReceiverData,
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Producer & Receiver not found'
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
                'status' => 1,
                'message' => 'Producer & Receiver added successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    public function show($id)
    {
        $producerReceiver = ProducerReceiver::find($id);
        if (!$producerReceiver) {
            return response()->json([
                'status' => 0,
                'message' => 'Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Producer & Receiver fetch successfully',
            'results' => $producerReceiver,
        ], 200);
    }

    public function edit($id)
    {
        $data = ProducerReceiver::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $producerReceiver = ProducerReceiver::find($request->id);

        if ($request->hasFile('section_image')) {
            $file1 = $request->file('section_image');
            $imageName1 = date('ymdhis') . rand(1000, 100000) . '.png';
            $file1->move(public_path('/images/sectionProducerReceiver'), $imageName1);
        
            // Get the base URL
            $baseUrl = url('/');
            $producerReceiver->section_image = $baseUrl . '/images/sectionProducerReceiver/' . $imageName1;
        }
        $producerReceiver->section_title = $request->section_title;
        $producerReceiver->producer_title = $request->producer_title;
        $producerReceiver->producer_description = $request->producer_description;
        $producerReceiver->receiver_title = $request->receiver_title;
        $producerReceiver->receiver_description = $request->receiver_description;
        $producerReceiver->deleted_at = $request->has('deleted_at') ? $request['deleted_at'] : 0;
        $producerReceiver->update();

        return response()->json([
            'status' => 1,
            'message' => 'Producer & Receiver updated successfully',
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $deleteData = ProducerReceiver::find($request->id);
        // dd($deletemenu);
        if ($deleteData) {
            $deleteData->deleted_at = 1;
            if ($deleteData->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Producer & Receiver deleted successfully',

                ],200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No matching Producer & Receiver found for deletion',
        ],404);
    }
    }

