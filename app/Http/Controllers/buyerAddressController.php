<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\buyerAddress;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class buyerAddressController extends Controller
{


    public function create(Request $request) {

        $checkUser =   auth('api')->user();
        if(!$checkUser) {
            return ResponseFormatter::error($data =null, 'Please login ');
        };
        $validator = Validator::make($request->all(), [
            'complateAddress' => "required",
            'phoneNumber' => "required",
            'postalCode' => "required",
            'labelAddress' => 'required',
            'city_name' => 'required',
            'city_id' => 'required',
            'province_id' => 'required',
            'province_name' => 'required',
            'subdistrict' => 'required',
            'subdistrict_id' => 'required'
        ]);

        if ($validator->fails()) {
            return
            ResponseFormatter::error($validator->errors(), 'Failed');
        }

        $buyerAddress = buyerAddress::create(
            [
                'phoneNumber' => $request->phoneNumber,
                'postalCode' => $request->postalCode,
                'labelAddress' => $request->labelAddress,
                'province_id' => $request->province_id,
                'province_name' => $request->province_name,
                'city_id' => $request->city_id,
                'city_name' => $request->city_name,
                'subdistrict_id' => $request->subdistrict_id,
                'subdistrict' => $request->subdistrict,
                'village' => $request->village,
                'courirMessage' => $request->courirMessage,
                'complateAddress' => $request->complateAddress,
                'buyers_id' => $checkUser['id'],
            ]    
        
        );
        return ResponseFormatter::success($buyerAddress,'Alamat berhasil ditambahkan');

    }
    public function update(Request $request, $id) {
        $checkUser =   auth('api')->user();

        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };


        $buyerAddress = buyerAddress::find($id);
        if (!$buyerAddress) {
            return ResponseFormatter::error($data = null, 'Data alamat tidak ditemukan ');
        }
        if ($checkUser['id'] !== $buyerAddress['buyers_id']) {
            return ResponseFormatter::error($data = null, 'Aksi tidak bisa dilakukan ');
        }

        $buyerAddress->update(['complateAddress' => $request->complateAddress,
            'phoneNumber' => $request->phoneNumber,
            'postalCode' => $request->postalCode,
            'labelAddress' => $request->labelAddress,
            'province_id' => $request->province_id,
            'province_name' => $request->province_name,
            'city_id' => $request->city_id,
            'city_name' => $request->city_name,
            'subdistrict_id' => $request->subdistrict_id,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
            'courirMessage' => $request->courirMessage,
            'complateAddress' => $request->complateAddress,
            'buyers_id' => $checkUser['id'],
    ]);

    return ResponseFormatter::success($buyerAddress,'Alamat berhasil di update');
    }
    public function destroy($id) {
        $checkUser =   auth('api')->user();

        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };

        
        $buyerAddress= buyerAddress::find($id);
        if(!$buyerAddress){
            return ResponseFormatter::error($data = null, 'Data alamat tidak ditemukan ');
            
        }
        if($checkUser['id'] !== $buyerAddress['buyers_id']) {
            return ResponseFormatter::error($data = null, 'Aksi tidak bisa dilakukan ');

        }
        $buyerAddress->delete();
        

    }
}
