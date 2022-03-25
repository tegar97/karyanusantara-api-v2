<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\category;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;
use Intervention\Image\ImageManagerStatic;

class categoryController extends Controller
{

    public function view(){
        $categoryData = category::all();

        return ResponseFormatter::success($categoryData,'Category berhasil ditambahkan');
    }

    public function GetProductByCategory(Request $request){
        $city = $request->input('city');
        $multiCity = explode(',', $city);

        foreach ($multiCity as $c) {
            $categories = category::with('product.images', 'product.umkm:id,city_name,ukmName')->whereRelation("product.umkm", "city_name", "like", "%" . $c . "%")->get();
           
            // $product->whereRelation("umkm", "city", "LIKE", "%" . $c . "%");
            // $getProduct = $product->get();
            foreach($categories as $category) {
                $getProductByCity[] = array(
                    'categoryName' => $category['categoryName'],
                    'product' => $category['product']
                );
            }

          
            // foreach ($getProduct as $product) {
            //     $getProductByCity[] = array(
            //         'name' => $product['name'],
            //         'price' => $product['price'],
            //         'umkm' => $product['umkm'],
            //     );
            // }
        

        }
        return ResponseFormatter::success($getProductByCity,'Berhasil mendapakan produk');
    }
    public function detail($id) {
        $categoryData = category::where('id',$id)->with('subCategory')->get();

        return ResponseFormatter::success($categoryData, 'Category berhasil ditambahkan');
    }
    public function create(Request $request) {
        $data = $request->only('categoryName','categoryIcon');
        $validator = Validator::make($data, [
            'categoryName' => 'required|string',
            'categoryIcon' => 'required',
           
        ]);
        if ($validator->fails()) {
            return
                ResponseFormatter::error($validator->errors(), 'Failed');
        }
        $image      = $request->file('categoryIcon');
        $fileName   = time() . '.' . $image->getClientOriginalExtension();
        $getImageName = $this->resizeImage($image,$fileName);

        $categoryData = category::create([
            'categoryName' => $request->categoryName,
            'categoryIcon' => $getImageName
        ]);
  
        return ResponseFormatter::success($categoryData,'Kategori Berhasil ditambahkan');

        
    }
    public function destroy($id) {
        // Storage::delete('public/images/categoryIcon/20220318083039-category-png.png');
        $categoryData = Category::find($id);

        if($categoryData === null) {
            return ResponseFormatter::error($data = null,'Category tidak tersedia');
        }
        $PATH = $categoryData['categoryIcon'];

        // unlink(storage_path('categoryIcon/'.$PATH));
        Storage::delete('public/images/categoryIcon/'.$PATH);


        $categoryData->delete();
        return ResponseFormatter::success(null,'Success');

    }

    public function update(Request $request,$id){
        $categoryData = Category::where('id', $id)->first();
        if ($categoryData === null) {
            return ResponseFormatter::error($data = null, 'Category tidak tersedia');
        }
        $image      = $request->file('categoryIcon');
        $fileName   = time() . '.' . $image->getClientOriginalExtension();
        $getImageName = $this->resizeImage($image, $fileName);

        $categoryData->update(['categoryName' => $request->categoryName,
        'categoryIcon' => $getImageName,
        ]);
        return ResponseFormatter::success(null, 'Success');
    }
    public function resizeImage($file, $fileNameToStore)
    {
        // Resize image
        $resize = Image::make($file)->resize(80,80, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('png', 70);

        // Create hash value
        $hash = md5($resize->__toString());
        $var = date_create();
        $time = date_format($var, 'YmdHis');
        $imageName = $time . '-' . 'category'.'.'.'png';

        // Put image to storage
        $save = Storage::put("public/images/categoryIcon/{$imageName}", $resize);

        if ($save) {
            return $imageName;
        }
        return false;
    }
}

  

