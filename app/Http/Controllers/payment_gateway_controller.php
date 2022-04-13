<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\payment_gateway;
use Illuminate\Http\Request;

class payment_gateway_controller extends Controller
{
    public function create(Request $request) {
        $gateway = payment_gateway::create([
            'gateway_name' => $request->gateway_name,
            'gateway_code' =>  $request->gateway_code,
            'gateway_how_to_pay' => "-",
            'gateway_logo' => "dummy.png",
            'gateway_description' =>  $request->gateway_description
   
        ]);

        $gateway->gateway_logo = imageResizer::ResizeImage($request->gateway_logo, 'gateway', 'gateway-logo', 76, 76);
        $gateway->save();

    

        return ResponseFormatter::success('Berhasil');
    }

    public function view(){
        $gateway = payment_gateway::all();

        return ResponseFormatter::success($gateway,'Berhasil');

    }
}
