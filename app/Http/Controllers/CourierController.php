<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\courier;
use App\Models\courier_settings;
use App\Models\sub_service_courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function addCourier(Request $request){
        $courier = courier::create([
            'code' => $request->code,
            'name' => $request->name,
            'image' => 'dummy.png'
        ]);

        $courier->image = imageResizer::ResizeImage($request->image, 'couriers', 'courier-logo' , 76, 76);
        $courier->save();

        return ResponseFormatter::success('Sukses manambah layanan kurir');
    }

    public function getCourier(){
        $courier = courier::all();

        return ResponseFormatter::success($courier,'sukses');
    }

    public function addSubServiceCourier(Request $request){
        $subService = sub_service_courier::create([
            'service' => $request->service,
            'description' => $request->description,
            'courier_id' => $request->courier_id

        ]);

        return ResponseFormatter::success('Sukses manambah sub layanan kurir');

    }

    public function getMyCourier(Request $request){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $courier = courier_settings::where('umkm_id',$user['id'])->with('courier')->get();

        return ResponseFormatter::success($courier,'Sukses');

    }

    public function  settingCouriersUmkm(Request $request){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $courier_setting = courier_settings::where('courier_id',$request->courier_id)->where('umkm_id',$user['id'])->first();
        if($courier_setting !== null){
            $courier_setting->status = $request->status;
            $courier_setting->save();
        }else{
            courier_settings::create(['umkm_id' => $user->id,
                'courier_id' => $request->courier_id,
                'status' => $request->status
        ]);
        }
        // $settingCourierUmkm = courier_settings::updateOrCreate([
        //     'umkm_id' => $user->id,
        //     'courier_id' => $request->courier_id,
        //     'status' => $request->status
        // ]);

        return ResponseFormatter::success('Sukses');

    }
}
