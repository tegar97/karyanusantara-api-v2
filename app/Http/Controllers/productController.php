<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\category;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class productController extends Controller
{
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'description' => "required",
            'subcategory_id' => "required",
            'minimumOrder' => 'required',
            'price' => 'required',
            'weight' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }

       
        $productData = product::create($request->all());
        $user = auth('umkm')->user();

        

        if($user === null) {
            return ResponseFormatter::error('Please Login for continue ',401);
        }

        $productData->slug = $user['id']. $productData->id.'-'.Str::slug($request->name);
        $productData->umkm_id = $user['id'];
        $productData->save();
        return ResponseFormatter::success($productData, 'Success menambahkan product');
        
    }

    public function view(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $city = $request->input('city');
        $multiCity = explode(',', $city);
        foreach ($multiCity as $c){
            $product = product::with('umkm:id,ukmName,city_name');
      
            $product->whereRelation("umkm", "city_name", "LIKE", "%". $c ."%");
            $getProduct = $product->get();

            foreach($getProduct as $product) {
                $getProductByCity[] = array(
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'umkm' => $product['umkm'],
                    'category' => $product['category_id'],
                );

              
            }
        }

      
       
        return ResponseFormatter::success($getProductByCity, 'Success');

    }
    public function filterCategory(Request $request ){
        $category = $request->input('category');
        $subcategory = $request->input('subcategory');
        $province = $request->input('province');
        $multiSubCategory = explode(',', $subcategory);
        $multiProvince = explode(',', $province);
        if($province !== null){

            if ($category !== null) {
                if ($subcategory !== null) {
                    $product =    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->whereRelation('category', 'code', $category)->whereIn('subcategory_id', $multiSubCategory)->whereHas('umkm', function ($query) use ($multiProvince) {
                        $query->whereIn('province_id', $multiProvince);
                    })->get();
            
                } else {
                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->whereRelation('category', 'code', $category)->whereHas('umkm', function ($query) use ($multiProvince) {
                        $query->whereIn('province_id', $multiProvince);
                    })->get();
            
                }
            } else {

                if($subcategory !== null){

                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->whereIn('subcategory_id', $multiSubCategory)->whereHas('umkm', function ($query) use ($multiProvince) {
                        $query->whereIn('province_id', $multiProvince);
                    })->get();
                }else{

                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->whereHas('umkm', function ($query) use ($multiProvince) {
                        $query->whereIn('province_id', $multiProvince);
                    })->get();
                }

            
            }
        
        }else{

            if($category !== null){
                if ($subcategory !== null) {
                    $product = product::where('status', 2)->with('category', 'images','umkm:id,ukmName,province_id,province_name','category')->whereRelation('category','code',$category)->whereIn('subcategory_id', $multiSubCategory)->get();
                } else {
                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->whereRelation('category','code',$category)->get();
                }
            }else{
                if ($subcategory !== null) {
                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name', 'category')->whereIn('subcategory_id', $multiSubCategory)->get();
                } else {
                    $product = product::where('status', 2)->with('category', 'images', 'umkm:id,ukmName,province_id,province_name')->get();
                }

            }

        }
     
        return ResponseFormatter::success($product, 'Success');

    
    }
    public function updateStatus(Request $request,$id){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
         $product = product::where('id',$id)->first();
         if($product->umkm_id === $user['id']){
            $product->update(['status' => $request->status]);
         }else{
            return ResponseFormatter::success('fail');

         }
     
        return ResponseFormatter::success('sukses update');

    }

    public function myproducts(){
        $user = auth('umkm')->user();
        

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $products = product::where('umkm_id', $user['id'])->with('images');

        return ResponseFormatter::success($products->paginate(10),'berhasil');
    }
    public function underReviewProduct(){
        $user = auth('admin')->user();
        

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $products = product::with('images','umkm:id,ukmName,email,ownerPhoneNumber');

        return ResponseFormatter::success($products->paginate(10),'berhasil');
    }

    public function detail(Request $request,$slug) {


        $productData = product::with('images', 'review.buyers','umkm:id,ukmName,city_name,city_id,slug', 'umkm.courier.courier','category:id,categoryName','productVariant', 'productVariant.variantOption')->where('slug', $slug)->withAvg('review','stars')->get();
      
        // foreach($productData as $product){
        //     foreach($product['review'] as $review){
        //            $reviewTotal[] = array(
        //             $review['stars']
        //            );
               
        //     }
            

           
        // }

        // for($i=0; $i < count($reviewTotal); $i++) {
        //     $total = $reviewTotal[$i] ;

        // }
      
        if($productData === null){
            return ResponseFormatter::error(null, 'Tidak menemukan product', 404);
            
        }
  
        return ResponseFormatter::success($productData, 'Success');


    }


    // public function delete($id){
    //     $productData = product::with('umkm')->where('slug', $slug)->get();

    // }

    public function detailProductAdminAccess(Request $request, $slug)
    {


        $productData = product::with('images', 'review.buyers', 'umkm', 'umkm.courier.courier', 'category:id,categoryName')->where('slug', $slug)->withAvg('review', 'stars')->get();

        // foreach($productData as $product){
        //     foreach($product['review'] as $review){
        //            $reviewTotal[] = array(
        //             $review['stars']
        //            );

        //     }



        // }

        // for($i=0; $i < count($reviewTotal); $i++) {
        //     $total = $reviewTotal[$i] ;

        // }

        if ($productData === null) {
            return ResponseFormatter::error(null, 'Tidak menemukan product', 404);
        }

        return ResponseFormatter::success($productData, 'Success');
    }

    public function AcceptOrReject(Request $request, $id)
    {
        $user = auth('admin')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $product = product::where('id', $id)->first();
        if ($product->umkm_id !== null) {
            $product->update(['status' => $request->status]);
        } else {
            return ResponseFormatter::success('fail');
        }

        return ResponseFormatter::success('sukses update');
    }

    public function addMainProduct(Request $request,$id){
        $user = auth('admin')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $product = product::where('id', $id)->first();

        if ($product->umkm_id !== null) {
            $product->update(['isMainProduct' => $request->isMainAddress]);
        } else {
            return ResponseFormatter::error('fail');
        }
    }

    public function getMainProduct(){
        $product = product::where('isMainProduct',1 )->with('images','umkm')->get();

        return ResponseFormatter::success($product,'success');


    }

    public function updateStock(Request $request){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $product = product::where('id', $request->id)->where('umkm_id',$user['id'])->first();

        $product->stock = $request->stock;
        $product->save();

        return ResponseFormatter::success('berhasil');


    }
}
