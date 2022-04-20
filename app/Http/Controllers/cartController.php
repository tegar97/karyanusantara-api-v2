<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\cart;
use App\Models\itemCart;
use App\Models\product;
use App\Models\umkm;
use Illuminate\Http\Request;

class cartController extends Controller
{
    public function create(Request $request){
        $checkUser =   auth('api')->user();

        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $product = product::where('id',$request->products_id)->first();
        //Validate Cart
            //TODO : IF Product has deleted
            if($product === null){
                return ResponseFormatter::error('product has deleted ',404);
            }
            //TODO : VALIDATE UMKM STORE 
            $umkm = umkm::find($product->umkm_id,'id')->first();
            if ($umkm === null) {
            return ResponseFormatter::error('Data umkm tidak ditemukan ', 404);
            }
            //TODO : IF no STOCK 
            if($product->stock == 0){
            return ResponseFormatter::error('upss stock telah habis', 404);
            }
            if($request->quantity < $product->minimum_buy){
            return ResponseFormatter::error('tidak memenuhi minimal pembelian', 404);

            }

        // create users cart
 
        $checkCart = cart::where('buyers_id',$checkUser['id'])->first();
        $total = $product->price * $request->quantity; 
        if($checkCart !== null){
            $checkCart->update([
                'total' => $checkCart->total + $total,
                'buyers_id' => $checkUser['id']
            ]);

            //Check if itemCart available on cart a
          $itemCart = itemCart::where('carts_id', $checkCart['id'])->where('products_id',$product['id'])->first();
          if($itemCart !== null){
              
              $itemCart->update(
                  ['products_id' => $product['id'],

                  'quantity' => $itemCart->quantity + $request->quantity,
                        'isSelected' => true,
                        'carts_id' => $checkCart->id,
                        'umkm_id' => $product['umkm_id'],

              ]);
   

          }else{

                itemCart::create([
                    'products_id' => $product['id'],
                    'quantity' => $request->quantity,
                    'isSelected' => true,
                    'carts_id' => $checkCart->id,
                    'umkm_id' => $product['umkm_id'],
                ]);
          }

           

     
        }else{
            //Get total 
            $cart = cart::create([
                'total' => $total,
                'buyers_id' => $checkUser['id']
            ]);

            itemCart::create([
                'products_id' => $product['id'],
                'quantity' => $request->quantity,
                'isSelected' => true,
                'carts_id' => $cart->id,
                'umkm_id' => $product['umkm_id'],

            ]);
        }
       
        return ResponseFormatter::success('Success ');

    
     
    }


    public function update(Request $request){
        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $carts = cart::where('buyers_id', $checkUser['id'])->with('itemCart.product:id,name,price', 'itemCart.umkm:id,ukmName')->first();

        $itemCart = itemCart::where('id', $request->itemCart_id)->first();
        if ($itemCart === null) {
            return ResponseFormatter::error('item cart not found', 404);
        }
        // $quantityTotal = $itemCart['quantity'] + $request->quantity;
        $product = product::where('id', $itemCart['products_id'])->first();

    
        //Validate Cart
        //TODO : IF Product has deleted
        if ($product === null) {
            return ResponseFormatter::error('product has deleted ', 404);
        }
        //TODO : VALIDATE UMKM STORE 
        $umkm = umkm::find($product->umkm_id, 'id')->first();
        if ($umkm === null) {
            return ResponseFormatter::error('Data umkm tidak ditemukan ', 404);
        }
        //TODO : IF no STOCK 
        if ($product->stock == 0 ) {
            return ResponseFormatter::error('upss stock telah habis', 400);
        }
        if ($request->quantity < $product->minimum_buy) {
            return ResponseFormatter::error('tidak memenuhi minimal pembelian', 404);
        }

        if($request->quantity <= 0){
            return  ResponseFormatter::error('Quantity tidak valid',400);

        }
       
            

        if($request->isSelected == 1){
            $itemCart->update([
                'products_id' => $itemCart['products_id'],
                'quantity' => $request->quantity,
                'isSelected' => $request->isSelected,
                'carts' => $itemCart['carts_id'],
                'umkm_id' => $product['umkm_id'],

            ]);

    

             $checkCart = cart::where('id',$itemCart['carts_id'])->with('itemCart.product:id,name,price', 'itemCart.umkm:id,ukmName')->get();

             $getItemCart = itemCart::where('carts_id',$checkCart)->with('product:id,price')->get();
            $total=0;
             foreach($checkCart as  $ItemCart){
                 foreach($ItemCart['itemCart'] as $each){

                    $total =  $total + ($each['quantity'] *  $each['product']['price']);

                 };

             };
         

            $carts->update(['buyers_id' => $checkUser['id'],
                'total' => $total

            ]);
           
            return ResponseFormatter::success($checkCart,'sukses update item 2');


        }else{
            $itemCart->update([
                'products_id' => $itemCart['products_id'],
                'quantity' => $itemCart['quantity'],
                'isSelected' => $request->isSelected,
                'carts' => $itemCart['carts_id'],
                'umkm_id' => $product['umkm_id'],

            ]);

            return ResponseFormatter::success('sukses update item select');

        }
      
    }
    public function deleteItem(Request $request,$id){
        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $carts = cart::where('buyers_id', $checkUser['id'])->with('itemCart.product:id,name,price', 'itemCart.umkm:id,ukmName')->first();
        $itemCart = itemCart::where('id', $id)->with('product:id,price')->first();

        if($itemCart === null) {
            return ResponseFormatter::error($data = null, 'Item telah dihapus dari cart ');

        }

        $carts->update([
            'buyers_id' => $checkUser['id'],
            'total' => $carts->total -  $itemCart['quantity'] * $itemCart['product']['price']
        ]);

        $itemCart->delete();




        return ResponseFormatter::success($carts, 'success ');


    }
    public function GetMyCart(Request $request) {
        $checkUser =   auth('api')->user();
  
        $carts = cart::where('buyers_id', $checkUser['id'])->with('itemCart.product:id,name,price,weight,stock,minimumOrder,category_id', 'itemCart.umkm:id,ukmName,city_id', 'itemCart.umkm.courier.courier' ,'itemCart.product.images','itemCart.product.category')->first();
      
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        // $carts = cart::with('itemcart.umkm:id,ukmName')->where('buyers_id', $checkUser['id'])->groupBy('itemcart.umkm_id')->get();
        // foreach($carts as $cart) {
        //     foreach($cart['itemcart'] as $itemcart){
        //         $umkm = umkm::where('id',$itemcart['umkm_id'])->with('product')->select('id','ukmName')->first();

               

        //     }
        //     $allcart[] =  [
                
        //         $umkm
        //     ];

         
        // }
        // $getItemCart = itemCart::where('carts_id',$carts['id'])->with('product')->get();
     
        // $getItemCart = umkm::with('itemCart')->whereRelation('itemCart', 'carts_id', $carts['id'])->get();
        // foreach($getItemCart as $cart){
        //     foreach($cart->itemCart as $item){
        //         $filter = $item->carts_id === $carts['id'];
        //         if($filter === true) {
        //             $getAll = [
        //                 'umkm' => [
        //                     $cart,
        //                     'product' =>$cart->itemCart = $item

        //                 ]
        //                 ];
        //             $group[] = array(
        //                 $getAll
                        
        //             );
        //         }
        //     }

          
        // }
     
        // foreach($getItemCart as $cart){
        //     $product = product::where('id',$cart['products_id'])->get();

        //     $umkm = umkm::where('id', '=', $cart['umkm_id'])->get();
        //     $getListUmkm[] = array(
        //         $umkm
        //     );

           

            
        // }

            
        // }

        // find umkm store

        // foreach($getItemCart as $cart) {

        //     $products = product::where('id', $cart['products_id'])->first();
       
        //     $getPrice = $products->price * $cart['quantity'];
        //     if ($products->stock < 1) {
                
        //         $carts->update([
        //             'buyers_id' => $checkUser['id'],
        //             'total' => $carts->total - $getPrice
        //         ]);
        //         $cart->delete();
             
                
        //     }

        //     $groupProduct[] = array(
        //         'quantity' => $cart['quantity'],
        //         $products
        //     );

           

        // }

        return ResponseFormatter::success($carts);


    }

    public function addCourier(Request $request) {
        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $itemCart = itemCart::where('id', $request->id)->first();

        $itemCart->update(['products_id' => $itemCart['products_id'],
            'quantity' => $itemCart['quantity'],
            'isSelected' => $request->isSelected,
            'carts' => $itemCart['carts_id'],
            'umkm_id' => $itemCart['umkm_id'],
            'service_courier' => $request->service_courier,
            
        ]);

    }
    
}
