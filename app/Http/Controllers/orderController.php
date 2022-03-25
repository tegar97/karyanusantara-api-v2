<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\order;
use App\Models\ordersproduct;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class orderController extends Controller
{
    public function NewOrder(Request $request) {

        $order = order::create([
            'buyers_id' => $request->buyers_id,
            'payment' => $request->payment,
            'courir' => $request->courir,
            'total_quantity' => $request->total_quantity,
            'total_amount' => $request->total_amount,
            'status' => $request->status
        ]);

        $itemTotal = [];
        foreach($request->cart as $item) {
            $checkProductData = product::find($item['product_id']);
            dump($checkProductData);


           $orderProduct=  ordersproduct::create([
                'order_id' => $order->id,
                'product_id' =>$checkProductData->id,
                'quantity' => $item['quantity'],
                'price' =>$checkProductData->price,
                'name' => $checkProductData->name,

            ]);
            $itemTotal[] =  array(
                'id' => $orderProduct->id,
                'price' => $checkProductData->price,
                'quantity'=> $item['quantity'],
                'name' => $checkProductData->name,
            );
        }
     
        dump($order);
        

        $midtransParams = [
            'transaction_details' =>  [
                'order_id' => $order->id . '-' . Str::random(5),
                'gross_amount' => $request->total_amount,
            ],
            'item_details' => $itemTotal,
            'customer_details' => [
                'email' => 'tegar@gmail.com',
                'first_name' => "tegar"
            ]
        ];

        $midtransSnapUrl = $this->getMidtransSnapshot($midtransParams);

        return ResponseFormatter::success($midtransSnapUrl,'berhasil');

    }

    private function getMidtransSnapshot($params){
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_ISPRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('IS3DS');

        $snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
        return $snapUrl;

    }
}
