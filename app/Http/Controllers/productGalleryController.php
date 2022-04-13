<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\productGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class productGalleryController extends Controller
{
  public function create(Request $request)
  {
    $data = [];
    $product_id = $request->input('id');
    foreach ($request->file('images') as $key => $file) {
      // dd($key);
      $getImageName = imageResizer::ResizeImage($file, 'product', 'product-photo' . $key, 600, 600, "jpg", 100);
      $data[$key] = $getImageName;
      $categoryData = productGallery::create([
        'imageName' => $getImageName,
        'product_id' => $product_id,
        'url' => config('app.url') . Storage::url("public/images/$getImageName"),
      ]);
    }


    // $getImageName
    // = imageResizer::ResizeImage($image, 'product', 'product-photo', 300, 300,"jpg");

    return ResponseFormatter::success($data, 'Success upload gambar');
  }

  public function singleUpload(Request $request)
  {

    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(1000000, 9999999)
      . mt_rand(1000000, 9999999)
      . $characters[rand(0, strlen($characters) - 1)];

    // shuffle the result
    $string = str_shuffle($pin);
    $getImageName = imageResizer::ResizeImage($request->file('images'), 'product', 'product-photo' . $string, 700, 700, "jpg", 100);
    productGallery::create([
      'imageName' => $getImageName,
      'product_id' => $request->product_id,
      'url' => config('app.url') . Storage::url("public/images/$getImageName"),
    ]);
    return ResponseFormatter::success('Success upload gambar');

  }

  public function delete(Request $request)
  {
  }
}
