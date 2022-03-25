<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\productGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class productGalleryController extends Controller
{
    public function create(Request $request){
    $data = [];

      foreach($request->file('images') as $key=>$file) {
        // dd($key);
        $getImageName = imageResizer::ResizeImage($file, 'product', 'product-photo'.$key, 120, 120);
      $data[$key] = $getImageName;
        $categoryData = productGallery::create([
          'imageName' => $getImageName,
          'product_id' => $request->product_id,
          'url' => config('app.url') . Storage::url("public/images/$getImageName"),
        ]);


      }


    // $getImageName
    // = imageResizer::ResizeImage($image, 'product', 'product-photo', 300, 300,"jpg");

  
    return ResponseFormatter::success($data,'Success upload gambar');
  
    } 

    public function delete(Request $request) {
      
    }
}
