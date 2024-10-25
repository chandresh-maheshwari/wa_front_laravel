<?php

namespace App\Http\Controllers;

use App\Models\ServiceModel;
use App\Models\TestimonialModel;

class CommansearchController extends Controller
{
    //

    public function search($name)
    {
        $result1 = ServiceModel::where("service_title", "like", "%" . $name . "%")
            ->orWhere("description", "like", "%" . $name . "%")
            ->get();
        // $result2 = TestimonialModel::where("testimonial", "like", "%" . $name . "%")
        //     ->orWhere("add_by", "like", "%" . $name . "%")
        //     ->orWhere("position", "like", "%" . $name . "%")
        //     ->get();
        // $result3 = $result1 . $result2;

        return ($result1);
    }
}
