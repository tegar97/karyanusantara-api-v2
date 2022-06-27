<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\item_carts_variant;
use Illuminate\Http\Request;

class itemCartsVariantController extends Controller
{
    public function create(Request $request) {

        $checkUser =   auth('api')->user();

        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        item_carts_variant::create([
            'itemCarts_id' => $request->itemCarts_id,
            'product_variantion_id' => $request->product_variantion_id,
            'variants_option_id' => $request->variants_option_id
        ]);

        return ResponseFormatter::success('Success ');

    }
}
