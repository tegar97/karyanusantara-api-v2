<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\product;
use App\Models\productVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class productVariantsController extends Controller
{
    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'variantName' => 'required',
            'type' => "required",

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }
        $checkUser =   auth('umkm')->user();

        if ($checkUser === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $productCheck = product::where('id',$request->product_id)->where('umkm_id',$checkUser['id'])->first();

        if($productCheck === null){
            return ResponseFormatter::error(null, 'Produk tidak ditemukan');

        }

        $productVariant = productVariants::create([
            'product_id' => $request->product_id,
            'variantName' => $request->variantName,
            'type' => $request->type
        ]);

        return ResponseFormatter::success($productVariant,'sukses menambah variant product');
        
    }
}
