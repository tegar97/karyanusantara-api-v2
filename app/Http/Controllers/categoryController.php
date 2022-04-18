<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\category;
use App\Models\product;
use App\Models\subCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;
use Intervention\Image\ImageManagerStatic;
use PhpParser\Node\Stmt\Foreach_;

class categoryController extends Controller
{

    public function view(){
        $categoryData = category::all();

        return ResponseFormatter::success($categoryData,'Category berhasil ditambahkan');
    }
    

    public function GetProductByCategory(Request $request){
        $city = $request->input('city');
        $category = $request->input('category');
        $subCategory = $request->input('subcategory');
        $multiCity = explode(',', $city);
        $multiSubCategory = explode(',', $subCategory);
        $getProductByCity = [];
        foreach ($multiCity as $c) {

            if($category !== null){
                $categories = category::with('product.images', 'product.umkm:id,city_name,ukmName')->where('code', $category)->WhereRelation("product.umkm", "province_name", "like", "%" . $c . "%")->whereRelation('product', 'status', 2)->get();

            }else{
                $categories = category::with('product.images', 'product.umkm:id,city_name,ukmName')->WhereRelation("product.umkm", "province_name", "like", "%" . $c . "%")->whereRelation('product', 'status', 2)->get();

            }
           
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
        

        };
        // foreach($multiSubCategory as $sub){
        //     if ($sub !== null) {
        //         $categories = category::with('product.images', 'product.umkm:id,city_name,ukmName')->WhereRelation('categoryName', $category)->WhereRelation("product.umkm", "province_name", "like", "%" . $c . "%")->whereRelation('product', 'status', 2)->get();
        //     } else {
        //         $categories = category::with('product.images', 'product.umkm:id,city_name,ukmName')->WhereRelation("product.umkm", "province_name", "like", "%" . $c . "%")->whereRelation('product', 'status', 2)->get();
        //     }

        //     // $product->whereRelation("umkm", "city", "LIKE", "%" . $c . "%");
        //     // $getProduct = $product->get();
        //     foreach ($categories as $category) {
        //         $getProductByCity[] = array(
        //             'categoryName' => $category['categoryName'],
        //             'product' => $category['product']
        //         );
        //     }
        // }

            return ResponseFormatter::success($getProductByCity,'Berhasil mendapakan produk');
      
    }

    public function getCategoryAndSubcategory(){
        $category = category::with('subCategory')->take(7)->get();

        return ResponseFormatter::success($category, 'Berhasil');

    }
    public function detail($id) {
        $categoryData = category::where('id',$id)->with('subCategory')->get();

        return ResponseFormatter::success($categoryData, 'Category berhasil ditambahkan');
    }
    public function create(Request $request) {
        $data = $request->only('categoryName','categoryIcon');
        $validator = Validator::make($data, [
            'categoryName' => 'required|string',
           
        ]);
        if ($validator->fails()) {
            return
                ResponseFormatter::error($validator->errors(), 'Failed');
        }
     

        $categoryData = category::create([
            'categoryName' => $request->categoryName,
            'code' => $request->categoryCode,
            'categoryIcon' => '-'
        ]);
  
        return ResponseFormatter::success($categoryData,'Kategori Berhasil ditambahkan');

        
    }

    public function uploadIcon(Request $request,$id){
        $image      = $request->file('categoryIcon');
        $fileName   = time() . '.' . $image->getClientOriginalExtension();
        $getImageName = $this->resizeImage($image, $fileName);

        $category = category::where('id',$id)->first();

        $category->categoryIcon = $getImageName;
        $category->save();

        return ResponseFormatter::success($category,'Icon berhasil ditambahkan');


    }
    public function destroy($id) {
        // Storage::delete('public/images/categoryIcon/20220318083039-category-png.png');
        $categoryData = Category::where('id',$id)->first();
        $subCategories = subCategory::where('category_id', $categoryData->id)->get();
        $products = product::where('category_id', $categoryData->id)->get();
        if($categoryData === null) {
            return ResponseFormatter::error($data = null,'Category tidak tersedia');
        }
        $PATH = $categoryData['categoryIcon'];

        // unlink(storage_path('categoryIcon/'.$PATH));
        Storage::delete('public/images/categoryIcon/'.$PATH);

        foreach ($subCategories as $subCategory){
            $subCategory->delete();
        }
        foreach($products as $product) {
            $product->delete();
        }
        $categoryData->delete();

        
        return ResponseFormatter::success(null,'Success');

    }

    public function update(Request $request,$id){
        $categoryData = Category::where('id', $id)->first();
        if ($categoryData === null) {
            return ResponseFormatter::error($data = null, 'Category tidak tersedia');
        }
     
        $categoryData->categoryName = $request->categoryName;
        $categoryData->code = $request->categoryCode;
        $categoryData->save();
        return ResponseFormatter::success($categoryData, 'Success');
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

    public function getCategoriesWithSub(){
        $categoryData = category::with('subCategory')->select('id','categoryName','categoryIcon')->get();

        return ResponseFormatter::success($categoryData,'sukses');

    }
}

  

