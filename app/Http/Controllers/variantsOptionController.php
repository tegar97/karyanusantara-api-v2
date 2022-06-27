<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\product;
use App\Models\productGallery;
use App\Models\productsStock;
use App\Models\productVariants;
use App\Models\variantsOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class variantsOptionController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'variantStock' => 'required',
            'variantNameOption' => 'required',
            'variantPrice' => "required",

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }
        $checkUser =   auth('umkm')->user();

        if ($checkUser === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $productStock = productsStock::create([
            'totalStock' => $request->variantStock,
            'unitPrice' => $request->variantPrice
        ]);

        $productVariantOption = variantsOption::create([
            'variantName' => $request->variantNameOption,
            'product_variantion_id' => $request->productVariantionId,
            'sku' => $request->variantSku,
            'variantionImg' => '0.png',
            'price' => $request->variantPrice,
            'product_stock_id' => $productStock->id,
        ]);
        

        $productId = product::where('id',$request->productVariantionId)->first();

        if($request->variantionImg){
            $getImageName = imageResizer::ResizeImage($request->variantionImg, 'product', 'product-photo', 600, 600, "jpg", 100);
            $productVariantOption->variantionImg =  $getImageName;

            $productVariantOption->save();

            productGallery::create([
                'imageName' => $getImageName,
                'product_id' => $productId['id'],
                'url' => config('app.url') . Storage::url("public/images/$getImageName"),
            ]);
        }
       
        return ResponseFormatter::success($productVariantOption, 'sukses menambah variant optin');

    }
}
