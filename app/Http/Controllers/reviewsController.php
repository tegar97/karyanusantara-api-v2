<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class reviewsController extends Controller
{

    
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'stars' => 'required',
            'product_id' => 'required',
            'review' => "required",
          
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }
        $checkUser =   auth('api')->user();

        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };


        //TODO : CHECK IF USER HAS BOUGHT PRODUCT
        
        //
         review::create([
            'stars' => $request->stars,
            'products_id' => $request->product_id,
            'review' => $request->review,
            'buyers_id' => $checkUser['id']
        ]);

        return ResponseFormatter::success('Sukses menambah reviews ');

    }
}
