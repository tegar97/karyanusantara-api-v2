<?php

use App\Models\order;
use App\Models\ordersproduct;
use App\Models\payment;
use App\Models\payment_gateway;

class StoreToPaymentDb
{

    public static function store($response,$user,$request,$fieldModify1,$fieldModify2)
    {
        $gatewayCode = payment_gateway::where('gateway_code', $request->payment_code)->first();
        $expire = strtotime('+1 days');
        $dateString = date('r', $expire);

        $payment = payment::create([
            'buyers_id' => $user['id'],
            'amount' => $response->gross_amount,
            'payment-url' => $response->transaction_id,
            'expire_time_unix' => $expire,
            'expire_time_str' => $dateString,
            'payment_status' => 2,
            'snap_url' => '-',
            'paymet_gateway_id' => $gatewayCode['id'],
            'payment_code' => $fieldModify1,
            'payment_key' => $fieldModify2,
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
       
    }
}