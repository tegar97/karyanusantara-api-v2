<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\city;
use App\Models\province;
use App\Models\subdistricts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class rajaOngkirController extends Controller
{
    public function getProvince(Request $request){
        
        $province = province::query();

        $response = Province::all();


        $q = $request->query('q');

        $province->when($q,function($query) use ($q){
            return $query->whereRaw("province_name LIKE '%".strtolower($q)."%'");
        });
        





        return ResponseFormatter::success($province->get(),'sukses');

    }
    public function getCity(Request $request){
        
        $city = city::query();



        $q = $request->query('q');

        $city->when($q,function($query) use ($q){
            return $query->whereRaw("city_name LIKE '%".strtolower($q)."%'");
        });






        return ResponseFormatter::success($city->get(),'sukses');

    }

    public function getsubdistricts(Request $request){
        $subdistricts = subdistricts::query();
        $q = $request->query('q');


        $subdistricts->when($q, function ($query) use ($q) {
            return $query->whereRaw("subdistrict_name LIKE '%" . strtolower($q) . "%'");
        });
        return ResponseFormatter::success($subdistricts->get(),'sukses');



    }


    public function fastSearch(Request $request) {
        $fastSearch = subdistricts::query();
        $q = $request->query('q');

        $fastSearch->when($q, function ($query) use ($q) {
            return $query->whereRaw("subdistrict_name LIKE '%" . strtolower($q) . "%'");
        });
      
    
        // Get cities 
        foreach ($fastSearch->get(['city_id', 'subdistrict_name', 'subdistrict_id']) as $subdistrict ){
            // dump($subdistrict['city_id']);
            $getCities = city::where('city_id', $subdistrict['city_id'])->get(['city_name','city_id','province_id']);

            // dump($getCity);

            foreach($getCities as $getCity) {
                $getProvinces = province::where('province_id', $getCity['province_id'])->get(['province_id','province_name']);
                foreach($getProvinces as  $getProvince) {

                    $city[] =  array(
                        'province_name' => $getProvince['province_name'],
                        'province_id' => $getProvince['province_id'],
                        'city_name' => $getCity['city_name'],
                        'city_id' => $getCity['city_id'],
                        'subdistrict_name' => $subdistrict['subdistrict_name'],
                        'subdistrict_id' => $subdistrict['subdistrict_id'],
                        'status' => 200



                    );

                   
                    
                }
               
            }
            
        };
        // $province = province::where('province_id', $city->province_id)->first();

        // $groupData = array(
        //     'subdistricts_name' => $subdistricts->subdistrict_name,
        //     'city_name' => $city->city_name,
        //     'province_name' => $province->province_name,
        //     'subdistricts_id' => $subdistricts->subdistrict_id,
        //     'city_id' => $city->city_id,
        //     'province_id' => $province->province_id,
        //     'status' => 200
        // );

        if($city === null) {
            return ResponseFormatter::error('Wilayah tidak tersedia');
        }

        return ResponseFormatter::success($city,'sukses');


    }

    public function cekOngkir(Request $request ){

        $courierGroup = explode(',',$request->courier);
        $isLowerPrice = $request->input('isLowerPrice');
        if(count($courierGroup) === 1){
            $response = Http::withHeaders(['key' => env('RAJA_ONGKIR_KEY')])->post('https://api.rajaongkir.com/starter/cost', [
                'origin'            => $request->origin,
                'destination'       => $request->destination,
                'weight'            => $request->weight,
                'courier'           => $request->courier
            ]);
            $ongkir = $response['rajaongkir']['results'];
        }else{
            foreach($courierGroup as $courier){
                $response = Http::withHeaders(['key' => env('RAJA_ONGKIR_KEY')])->post('https://api.rajaongkir.com/starter/cost', [
                    'origin'            => $request->origin,
                    'destination'       => $request->destination,
                    'weight'            => $request->weight,
                    'courier'           => $courier
                ]);

                $getCost = $response['rajaongkir']['results'];

         

                
                $ongkir[] = array(
                    $courier => $getCost
                );
            }

        }
        
       

        return response()->json([
            'success' => true,
            'message' => 'Result Cost Ongkir',
            'data'    => $ongkir
        ]);

    }

  
}
