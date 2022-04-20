<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\buyer;
use App\Models\buyerAddress;
use App\Models\order;
use App\Models\ordersproduct;
use App\Models\payment;
use App\Models\paymentlog;
use App\Models\product;
use App\Models\transction;
use App\Models\transction_item;
use Illuminate\Http\Request;

class webHookController extends Controller
{
    public function midtransHandler(Request $request){
        $data = $request->all();

        $signatureKey = $data['signature_key'];
        $orderId = $data['order_id'];
        $statusCode = $data['status_code'];
        $grossAmount = $data['gross_amount'];
        $serverKey = env('MIDTRANS_SERVER_KEY');


        $mySignatureKey = hash('sha512',$orderId.$statusCode.$grossAmount.$serverKey);

        $transactioStatus = $data['transaction_status'];
        $type = $data['payment_type'];
        $fraudStatus = $data['fraud_status'];


        if($signatureKey !== $mySignatureKey) {
            return ResponseFormatter::error('invalid signature',400);
        };
        $payment = payment::where('midtrans_order_id',$orderId)->first();
        $order = order::where('payments_id',$payment->id)->with('orderItem')->get();
        $buyers = buyer::where('id',$payment->buyers_id);

        if($payment->payment_status === 1){
            return ResponseFormatter::error('Operation not permitted',405);
        }

        if ($transactioStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // TODO set transaction status on your database to 'challenge'
                $payment->payment_status = 3;
                $payment->payment_status_str = 'challenge';

                // and response with 200 OK
            } else if ($fraudStatus == 'accept') {
                // TODO set transaction status on your database to 'success'
                // and response with 200 OK
                $payment->payment_status = 1;
                $payment->payment_status_str = 'settlement';


            }
        } else if ($transactioStatus == 'settlement') {
            // TODO set transaction status on your database to 'success'

            $buyers = buyer::where('id', $payment->buyers_id)->first();
            $address = buyerAddress::where('buyers_id', $payment->buyers_id)->first();
            foreach($order as $o){
                $transction = transction::create([
                    "invoice" => "INV"."/".date('Ymd'). "/" . $order->id,
                    "amount" => $o->amount,
                    "shipping_amount" => $o->shipping_amount,
                    "logistic_code" => $o->logistic_code,
                    "logistic_type" => $o->logistic_type,
                    "umkm_id" => $o->umkm_id,
                    'buyers_complate_address' => $buyers->name.' '.$address->phoneNumber . ' ' . $address->complateAddress . ' ' . $address->subdistrict . ' ' . $address->city_name . ' ' . $address->province_name,
                    "buyers_id" => $payment->buyers_id,
                    "payment_id" => $payment->id,
                    'status' => 2,
                    'status_str' => 'DIPROSES'
                ]);

                foreach($o['orderItem'] as $item){
                   $product =  product::where('id',$item->product_id)->first();
                   $product->stock =  $product->stock - $item->quantity;
                   $product->save();

                    transction_item::create([
                        'transaction_id' => $transction->id,
                        'quantity' => $item->quantity,
                        'amount' => $item->amount,
                        'product_id' => $item->product_id,
                    ]);
                }

            };
            
            $payment->payment_status = 1;
            $payment->payment_status_str = 'settlement';


            // and response with 200 OK
        } else if (
            $transactioStatus == 'cancel' ||
            $transactioStatus == 'deny' ||
            $transactioStatus == 'expire'
        ) {
            // TODO set transaction status on your database to 'failure'
            $payment->payment_status = 3;
            $payment->payment_status_str = 'cancel';

            // and response with 200 OK
        } else if ($transactioStatus == 'pending') {
            // TODO set transaction status on your database to 'pending' / waiting payment
            $payment->payment_status = 2;
            $payment->payment_status_str = 'pending';

            // foreach ($order as $o) {
            //     $value = $order['amount'] + $order['shipping_amount'];
            //     $email = $user

            //     foreach ($o['orderItem'] as $item) {
                  
            //     }
            // };
            


            // and response with 200 OK
        }
        $payment->save();
        $logData = [
            'payment_status_str' => $transactioStatus,
            'payment_status_int' => $payment->payment_status,
            'payments_id' => $payment->id,
            'payment_type' => $type,
            'raw_response' => json_encode($data)

        ];

        paymentlog::create($logData);
        return true;
    }

}
