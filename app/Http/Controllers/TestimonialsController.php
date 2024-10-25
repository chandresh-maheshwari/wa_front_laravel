<?php

namespace App\Http\Controllers;
use App\Models\TestimonialModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class TestimonialsController extends Controller
{
    
    public function testionmonial()
    {

        // dd("hello");
        return view('testimonials.testimonial_create');
    }
    public function store(Request $request)
    {
        $data = $request->all();

        $testimonial = new TestimonialModel();
        $testimonial->testimonial = $data['testimonial'];
        $testimonial->add_by = $data['add_by'];
        $testimonial->position = $data['position'];
        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial saved successfully',
        ]);
    }


    public function Testimoniallist()
    {
        return TestimonialModel::all();
    }


    public function testimonial_list() {

        // $testimonial = TestimonialModel::all();
        //  dd($testimonial);
      
        return view('testimonials.testimonial_list');
    }

    public function show() {

        $data = TestimonialModel::all();

        // $data2 = json_decode($data);
        return response()->json($data);
    }

    public function destroy($id) {


        $data = TestimonialModel::find($id)->delete();
        
        if($data){
            return response()->json(['code'=>1, 'msg'=>'Testimonial Delete Successfully']);
        }else{
            return response()->json(['code'=>0, 'msg'=>'Something went wrong']);
        }
    }


    public function Testimonialedit($id)
    {
        $data = TestimonialModel::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {

        // dd($request);
        $testimonial = TestimonialModel::find($request->id);
        // dd($testimonial);
        return response()->json($testimonial);
    }

    public function update_save(Request $request)
    {

        // dd($request->all());
// dd($request);
        $data = TestimonialModel::
              where('id', $request->id)  // find your user by their email
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update([
                'testimonial' => $request['testimonialtextarea'],
                'add_by' => $request['add_by'],
                'position' => $request['position'],
                // 'calloff_cost' => $request['calloff_cost'],
                // 'calloff_units' => $request['calloff_units'],
                // 'calloff_active' => $request['calloff_active'],

            ]);
            // dd($data);

            return response()->json([
                'data' => $data,
                'success' => true,
                'message' => 'Testimonial Updated successfully',
            ]);
            // return $data;
    }








    public function testimonialupdate(Request $request, $id)
    {       
    
        $testimonial = TestimonialModel::find($id);

            $testimonial->testimonial = $request->testimonial;
            $testimonial->add_by = $request->add_by;
            $testimonial->position = $request->position;
            $testimonial->update();
            return response()->json([
                'success' => 200,
                'message' => 'Services Update successfully',
            ]);
    }

    

    public function Testimonialdestroydata(Request $request, $ids)
    {

        // dd($request->all());
        try {
            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No IDs provided for deletion', 'code' => 400]);
            }
    
            // Convert the comma-separated string to an array of IDs
            $idArray = explode(',', $ids);
    
            // Perform the deletion
            $deletedRows = TestimonialModel::whereIn('id', $idArray)->delete();
    
            if ($deletedRows > 0) {
                return response()->json(['status' => 'success', 'message' => 'Clients deleted successfully', 'code' => 200]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No matching clients found for deletion', 'code' => 404]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => 500]);
        }
    }

}
