<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\buyer;
use App\Models\cart;
use App\Models\itemCart;
use App\Models\order;
use App\Models\ordersproduct;
use App\Models\payment;
use App\Models\payment_gateway;
use App\Models\product;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class orderController extends Controller
{
    public function orderMandiriBill(Request $request)
    {


        \Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_ISPRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('IS3DS');
        //1.Check if user still login
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        // Get payment code
        //Get Key
        // $key =base64_decode(env('MIDTRANS_SERVER_KEY'));

        $transaction_details = array(
            'order_id'    => time(),
            'gross_amount'  => $request->amount
        );

        $name = explode(' ', $user['name']);


        if (count($name) > 1) {
            $customer_details = array(
                'first_name'       => $name[0],
                'last_name'        => $name[1],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        } else {
            $customer_details = array(
                'first_name'       => $name[0],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        }

        $itemTotal = [];
        $nameProduct = [];
        $categoryId = [];

        foreach ($request->order_list as $orderList) {
            $ongkirTotal[] = array(
                'id' => rand(),
                'price' => $orderList['shipping_amount'],
                'quantity' => 1,
                'name' => 'Ongkir ' . $orderList['logistic_code'] . 'dengan service' . $orderList['logistic_type']
            );
            foreach ($orderList['item_list'] as $product) {
                $itemTotal[] =  array(
                    'id' => $product['product']['id'],
                    'price' => $product['product']['price'],
                    'quantity' => $product['quantity'],
                    'name' =>  $product['product']['name'],
                );
                // if ($user['username_lkpp' !== null]) {
                //     $nameProduct[] = $product['product']['name'];
                //     $categoryId[] = $product['product']['category']['id_kategori_lkpp'];
                // }

            }
        }
        // dd(implode(',',$nameProduct));

        $groupTotal = array_merge($ongkirTotal, $itemTotal);



        $echannel = array(
            "bill_info1" => "Payment:",
            "bill_info2" => "Pembelian di karyanusantara"
        );
        $transaction_data = array(
            'payment_type' => 'echannel',
            'transaction_details' => $transaction_details,
            'echannel' => $echannel,
            'item_details' => $groupTotal,
            'customer_details' => $customer_details

        );
        $response = \Midtrans\CoreApi::charge($transaction_data);


        $gatewayCode = payment_gateway::where('gateway_code', $request->payment_code)->first();
        //Add to payment 
        $expire = Carbon::now('Asia/Jakarta')->addDays(1)->timestamp;
        $dateString = Carbon::now('Asia/Jakarta')->addDays(1);

        $payment = payment::create([
            'buyers_id' => $user['id'],
            'midtrans_order_id' => $response->order_id,
            'amount' => $response->gross_amount,
            'payment_url' => $response->transaction_id,
            'expire_time_unix' => $expire,
            'expire_time_str' => $dateString,
            'payment_status' => 2,
            'snap_url' => '-',
            'paymet_gateway_id' => $gatewayCode['id'],
            'payment_code' => $response->biller_code,
            'payment_key' => $response->bill_key,
        ]);
        $lkPPTransaction = [];
        $changeToken = buyer::where('id',$user['id'])->first();
        foreach ($request->order_list as $orderList) {
            $order = order::create([
                'amount' => $orderList['amount'],
                'shipping_amount' => $orderList['shipping_amount'],
                'logistic_code' => $orderList['logistic_code'],
                'logistic_type' => $orderList['logistic_type'],
                'umkm_id' => $orderList['store_id'],
                'payments_id' => $payment['id']
            ]);


            foreach ($orderList['item_list'] as $product) {

                ordersproduct::create([
                    'orders_id' => $order->id,
                    'quantity' => $product['quantity'],
                    'product_id' => $product['product']['id'],
                    'amount' => $product['product']['price'] * $product['quantity'],
                ]);
            
            }
            if ($user['username_lkpp'] !== null) {
            

                $responseLkpp = Http::withHeaders(['X-Client-Id' => env('X_Client_Id'), 'X-Client-Secret' => env('X_Client_Secret')])->post(env('TOKODARING_TRANSACTION'),[
                    'valuasi' => $orderList['amount'] + $orderList['shipping_amount'],
                    'id_kategori' => $orderList['categoryId'],
                    'order_id' => "INV" . "/" . $order->id,
                    'order_desc' => implode(',', $orderList['orderDesc']),
                    'email' => $user['email'],
                    'phone' => $user['phoneNumber'],
                    'username' => $user['username_lkpp'],
                    'nama_merchant' => $orderList['merchent_name'],
                    'metode_bayar' => 'transfer',
                    'token' => $user['token_transaction_lkpp']
                ]);

                $changeToken->token_transaction_lkpp = $responseLkpp['new_token'];
                $changeToken->save();
                //   Http::withHeaders(['X-Client-Id' => '1234567890qwertyuiop',])->post(env('TOKODARING_DEV'));
 
            }

          
        };

   


        //Delete ccart
        $cart = cart::where('buyers_id', $user['id'])->first();
        $cart->total = 0;
        $cart->save();

        // if($user['username_lkpp' !== null]){
        //         $transactionData = array(
        //         'valuasi' => $request->amount + $request->shipping_amount,
        //         'id_kategori' => array_unique($categoryId),
        //         'order_id' => "INV" . "/" . date('Ymd') . "/" . $payment->id,
        //         'order_desc' => $nameProduct,
        //         'email' => $user['email'],
        //         'phone' => $user['phone'],
        //         'username' => $user['username'],
        //         'nama_merchant' => $user[]

        //         );
        // }

        itemCart::where('carts_id', $cart->id)->delete();

            if($user['username_lkpp'] !== null){
            return ResponseFormatter::success([
                'data' => $response,
                'data_lkpp' => $responseLkpp->json()
            ], 'berhasil');

            }else{

                return ResponseFormatter::success($response, 'berhasil');
            }
    }

    public function orderIndomaret(Request $request)
    {

        \Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_ISPRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('IS3DS');
        //1.Check if user still login
        $user = auth('api')->user();
        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        // Get payment code
        //Get Key
        // $key =base64_decode(env('MIDTRANS_SERVER_KEY'));

        $transaction_details = array(
            'order_id'    => time(),
            'gross_amount'  => $request->amount
        );

        $name = explode(' ', $user['name']);

        if (count($name) > 1) {
            $customer_details = array(
                'first_name'       => $name[0],
                'last_name'        => $name[1],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        } else {
            $customer_details = array(
                'first_name'       => $name[0],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        }
        $itemTotal = [];
        foreach ($request->order_list as $orderList) {
            $ongkirTotal[] = array(
                'id' => rand(),
                'price' => $orderList['shipping_amount'],
                'quantity' => 1,
                'name' => 'Ongkir ' . $orderList['logistic_code'] . 'dengan service' . $orderList['logistic_type']
            );
            foreach ($orderList['item_list'] as $product) {
                $itemTotal[] =  array(
                    'id' => $product['product']['id'],
                    'price' => $product['product']['price'],
                    'quantity' => $product['quantity'],
                    'name' =>  $product['product']['name'],
                );
            }
        }
        $groupTotal = array_merge($ongkirTotal, $itemTotal);


        $csstore = array(
            "store" => "indomaret",
            "message" => "Pembelian barang di karyanusantara"
        );
        $transaction_data = array(
            'payment_type' => 'cstore',
            'transaction_details' => $transaction_details,
            'cstore' => $csstore,
            'item_details' => $groupTotal,
            'customer_details' => $customer_details

        );
        $response = \Midtrans\CoreApi::charge($transaction_data);


        $gatewayCode = payment_gateway::where('gateway_code', $request->payment_code)->first();
        //Add to payment 
        $expire = Carbon::now('Asia/Jakarta')->addDays(1)->timestamp;
        $dateString = Carbon::now('Asia/Jakarta')->addDays(1);

        $payment = payment::create([
            'buyers_id' => $user['id'],
            'midtrans_order_id' => $response->order_id,
            'amount' => $response->gross_amount,
            'payment_url' => $response->transaction_id,
            'expire_time_unix' => $expire,
            'expire_time_str' => $dateString,
            'payment_status' => 2,
            'snap_url' => '-',
            'paymet_gateway_id' => $gatewayCode['id'],
            'payment_code' => $response->payment_code,
        ]);

        foreach ($request->order_list as $orderList) {
            $order = order::create([
                'amount' => $orderList['amount'],
                'shipping_amount' => $orderList['shipping_amount'],
                'logistic_code' => $orderList['logistic_code'],
                'logistic_type' => $orderList['logistic_type'],
                'umkm_id' => $orderList['store_id'],
                'payments_id' => $payment['id']
            ]);

            foreach ($orderList['item_list'] as $product) {
                ordersproduct::create([
                    'orders_id' => $order->id,
                    'quantity' => $product['quantity'],
                    'product_id' => $product['product']['id'],
                    'amount' => $product['product']['price'] * $product['quantity'],
                ]);
            }
        };


        //Delete ccart
        $cart = cart::where('buyers_id', $user['id'])->first();
        $cart->total = 0;
        $cart->save();

        itemCart::where('carts_id', $cart->id)->delete();

        return ResponseFormatter::success($response, 'berhasil');



        return ResponseFormatter::success($response, 'berhasil');
    }


    public function VirtualAccount(Request $request)
    {

        \Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_ISPRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('IS3DS');
        //1.Check if user still login
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        // Get payment code
        //Get Key
        // $key =base64_decode(env('MIDTRANS_SERVER_KEY'));

        $transaction_details = array(
            'order_id'    => time(),
            'gross_amount'  => $request->amount
        );

        $name = explode(' ', $user['name']);
        if (count($name) > 1) {
            $customer_details = array(
                'first_name'       => $name[0],
                'last_name'        => $name[1],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        } else {
            $customer_details = array(
                'first_name'       => $name[0],
                'email'            => $user['email'],
                'phone'            => $user['phoneNumber'],

            );
        }
        $itemTotal = [];
        foreach ($request->order_list as $orderList) {
            $ongkirTotal[] = array(
                'id' => rand(),
                'price' => $orderList['shipping_amount'],
                'quantity' => 1,
                'name' => 'Ongkir ' . $orderList['logistic_code'] . 'dengan service' . $orderList['logistic_type']
            );
            foreach ($orderList['item_list'] as $product) {
                $itemTotal[] =  array(
                    'id' => $product['product']['id'],
                    'price' => $product['product']['price'],
                    'quantity' => $product['quantity'],
                    'name' =>  $product['product']['name'],
                );
            }
        }
        $groupTotal = array_merge($ongkirTotal, $itemTotal);



        $bank_transfer = array(
            "bank" => $request->payment_code
        );
        $transaction_data = array(
            'payment_type' => 'bank_transfer',
            'transaction_details' => $transaction_details,
            'bank_transfer' => $bank_transfer,
            'item_details' => $groupTotal,
            'customer_details' => $customer_details

        );
        $response = \Midtrans\CoreApi::charge($transaction_data);


        $gatewayCode = payment_gateway::where('gateway_code', $request->payment_code)->first();
        //Add to payment 
        $expire = Carbon::now('Asia/Jakarta')->addDays(1)->timestamp;
        $dateString = Carbon::now('Asia/Jakarta')->addDays(1);

        $payment = payment::create([
            'buyers_id' => $user['id'],
            'midtrans_order_id' => $response->order_id,
            'amount' => $response->gross_amount,
            'payment_url' => $response->transaction_id,
            'expire_time_unix' => $expire,
            'expire_time_str' => $dateString,
            'payment_status' => 2,
            'snap_url' => '-',
            'paymet_gateway_id' => $gatewayCode['id'],
            'payment_key' => $response->va_numbers[0]->va_number,
        ]);
        $changeToken = buyer::where('id', $user['id'])->first();

        foreach ($request->order_list as $orderList) {
            $order = order::create([
                'amount' => $orderList['amount'],
                'shipping_amount' => $orderList['shipping_amount'],
                'logistic_code' => $orderList['logistic_code'],
                'logistic_type' => $orderList['logistic_type'],
                'umkm_id' => $orderList['store_id'],
                'payments_id' => $payment['id']
            ]);

            foreach ($orderList['item_list'] as $product) {
                ordersproduct::create([
                    'orders_id' => $order->id,
                    'quantity' => $product['quantity'],
                    'product_id' => $product['product']['id'],
                    'amount' => $product['product']['price'] * $product['quantity'],
                ]);
            }
            if ($user['username_lkpp'] !== null) {


                $responseLkpp = Http::withHeaders(['X-Client-Id' => env('X_Client_Id'), 'X-Client-Secret' => env('X_Client_Secret')])->post(env('TOKODARING_TRANSACTION'), [
                    'valuasi' => $orderList['amount'] + $orderList['shipping_amount'],
                    'id_kategori' => $orderList['categoryId'],
                    'order_id' => "INV" . "/".$order->id,
                    'order_desc' => implode(',', $orderList['orderDesc']),
                    'email' => $user['email'],
                    'phone' => $user['phoneNumber'],
                    'username' => $user['username_lkpp'],
                    'nama_merchant' => $orderList['merchent_name'],
                    'metode_bayar' => 'transfer',
                    'token' => $user['token_transaction_lkpp']
                ]);

                $changeToken->token_transaction_lkpp = $responseLkpp['new_token'];
                $changeToken->save();
                //   Http::withHeaders(['X-Client-Id' => '1234567890qwertyuiop',])->post(env('TOKODARING_DEV'));

            }
        };
        //Delete ccart
        $cart = cart::where('buyers_id', $user['id'])->first();
        $cart->total = 0;
        $cart->save();

        itemCart::where('carts_id', $cart->id)->delete();


        if ($user['username_lkpp'] !== null) {
            return ResponseFormatter::success([
                'data' => $response,
                'data_lkpp' => $responseLkpp->json()
            ], 'berhasil');
        } else {

            return ResponseFormatter::success($response, 'berhasil');
        }

    }

    private function getMidtransSnapshot($params)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_ISPRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('IS3DS');

        $snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
        return $snapUrl;
    }
}
