<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\transction;
use App\Models\transferLog;
use Illuminate\Http\Request;
use Midtrans\Transaction;

class transferLogController extends Controller
{
    public function create(Request $request){
        $user = auth('admin')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }
        $transferLog = transferLog::create([
            'umkm_id' => $request->umkm_id,
            'total' => $request->total,
            'cost_reduction' => json_encode($request->cost_reduction) ,
            'transaction_id' => $request->transaction_id,
            'status' => $request->status,

        ]);

        return ResponseFormatter::success($transferLog,'Berhasil');
    }

    public function getHistory() {
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $transaction = transction::where('umkm_id',$user['id'])->where('status',1)->with('transferLog')->select('invoice','amount', 'id', 'shipping_amount')->get();
        return ResponseFormatter::success($transaction, 'Berhasil');

    }
}
