<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\payment;
use Illuminate\Http\Request;

class paymentController extends Controller
{

    public function all(){
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }


        $payment = payment::where('buyers_id', $user['id'])->where('payment_status', 2)->with('paymentGateway')->get();

        return ResponseFormatter::success($payment, 'Berhasil');


    }
    public function get($code){

        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $payment = payment::where('payment_url',$code)->with('paymentGateway')->first();

        

        return ResponseFormatter::success($payment,'Berhasil');
    }

    public function getHistory(){
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }


        $payment = payment::where('buyers_id', $user['id'])->where('payment_status', 1)->with('paymentGateway')->get();

        return ResponseFormatter::success($payment, 'Berhasil');
    }

    public function detail($code){
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $payment = payment::where('payment_url', $code)->with('paymentGateway', 'order', 'order.umkm:id,ukmName', 'order.orderItem.product.umkm:id,ukmName')->first();
        return ResponseFormatter::success($payment, 'Berhasil');

    }
}
