<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\transction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Midtrans\Transaction;

class transctionController extends Controller
{
    public function getBuyerTransaction($status){
        $user = auth('api')->user();


        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $transcation = transction::where('buyers_id', $user['id'])->where('status', $status)->with('transactionItem.product:id,name,price', 'transactionItem.product.images', 'umkm:id,ukmName')->get();

        return ResponseFormatter::success($transcation,'berhasil');



        
    }

    public function getSellerTransaction($code){
        $user = auth('umkm')->user();

      
        $transcation = transction::where('umkm_id', $user['id'])->where('status', $code)->with('transactionItem.product:id,name,price', 'transactionItem.product.images', 'buyers:id,name')->get();
        return ResponseFormatter::success($transcation, 'berhasil');


    }

    public function sendResi(Request $request){

        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
     

      
        if ($request->courier == 'jne' || $request->courier == 'tiki' || $request->courier == 'pahala' ||   $request->courier == 'pandu'){
            $transcation = transction::where('id', $request->transaction_id)->first();
            $transcation->status = 3;
            $transcation->status_str = "DIKIRIM";
            $transcation->resi = $request->waybill;
            $transcation->save();
            return ResponseFormatter::success('success');
        }else {
            $response = Http::withHeaders(['key' => env('RAJA_ONGKIR_KEY')])->post('https://pro.rajaongkir.com/api/waybill', [
                'waybill'            => $request->waybill,
                'courier'       => $request->courier,
            ]);
            if($response->status() == 200){
                $getStatus = $response['rajaongkir']['result']['delivery_status']['status'];
                if ($getStatus === 'ON PROCESS') {
                    $transcation = transction::where('id', $request->transaction_id)->where('umkm_id', $user['id'])->first();
                    $transcation->status = 3;
                    $transcation->status_str = "DIKIRIM";
                    $transcation->resi = $request->waybill;
                    $transcation->save();
                }else{
                    return ResponseFormatter::error("Nomer resi tidak valid (terdeteksi pengiriman sudah selesai)", 'Nomer resi tidak valid (terdeteksi pengiriman sudah selesai)',400);
                }
            }else{
                return ResponseFormatter::error('Nomer resi salah', 'Nomer resi salah');

            }
           
        }
       

    }


    public function track(Request $request){
        $response = Http::withHeaders(['key' => env('RAJA_ONGKIR_KEY')])->post('https://pro.rajaongkir.com/api/waybill', [
            'waybill'            => $request->waybill,
            'courier'       => $request->courier,
        ]);
        $getStatus = $response['rajaongkir']['result'];

        return ResponseFormatter::success($getStatus,'Berhasil');

    }

    public function ComplateTheOrder(Request $request){

        $user = auth('api')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $transcation = transction::where('id', $request->transaction_id)->where('buyers_id',$user['id'])->first();
        $transcation->status = 1;
        $transcation->status_str = "SELESAI";
        $transcation->save();
    }


}
