<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\courier_settings;
use App\Models\umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class umkmController extends Controller
{
    public function register(Request $request ){
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'ukmName' => "required",
           
        ]);
        $userCheck = umkm::where('email', $request->email)->first();
        if ($userCheck !== null) {
            return ResponseFormatter::error(null, 'Email telah terdaftar');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }
        $myTTL = 300;
        JWTAuth::factory()->setTTL($myTTL);
        $umkmData = umkm::create([
            'email' => $request->email,
            'password' =>  bcrypt($request->password),
            'ukmName' => $request->ukmName,

        ]);
        $userCheck = umkm::where('slug',Str::slug($request->ukmName))->first();
     

        if($userCheck !== null){
            $umkmData->slug = Str::slug($request->ukmName)+$umkmData->id;

        }
        $umkmData->slug = Str::slug($request->ukmName);

        $umkmData->save();
        // Auto setting jne 
        courier_settings::updateOrCreate([
            'umkm_id' => $umkmData->id,
            'courier_id' => 1,
            'status' => 1
        ]);

        return ResponseFormatter::success($umkmData,'berhasil daftar');
        
    }

    public function update(Request $request) {
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id',$user['id'])->first();

        // $umkmData->update([
        //     'ukmName' => $request->ukmName,
        //     'description' => $request->description,
        //     'province_name' => $request->province_name,
        //     'province_id' => $request->province_id, 
        //     'city_name' => $request->city_name,
        //     'city_id' => $request->city_id, 
        //     'subdistrict' => $request->subdistrict,
        //     'subdistrict_id' => $request->subdistrict_id,
        //     'village' => $request->village, 
        //     'postalCode' => $request->postalCode,
        //     'ukmAddress' => $request->ukmAddress,
        //     'ukmOwner' => $request->ukmOwner,
        //     'BussinessFormType' => $request->BussinessFormType,
        //     'businessStart' => $request->businessStart,
        //     'ownerPhoneNumber' => $request->ownerPhoneNumber,
        //     'certificate' => $request->certificate,
        //     'certificateName' => $request->certificateName,
        //     'totalEmployee' => $request->totalEmployee,
        //     'annualIncome' => $request->annualIncome,
        //     'isInterestedToJoinUmkmid' => $request->isInterestedToJoinUmkmid
        // ]);
        if($request->file('profile')){
            $image      = $request->file('profile');
            $getImageName = imageResizer::ResizeImage($image, 'umkm-avatar', 'umkm-avatar', 120, 120);

            $umkmData->profile_photo = $getImageName;
            $umkmData->save();
        }
        return ResponseFormatter::success($umkmData, 'berhasil update');

    }

    public function storeEdit(Request $request ){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm',400);
        }
        $umkmData->ukmName = $request->ukmName;
        $umkmData->description = $request->description;
        $umkmData->province_id = $request->province_id;
        $umkmData->province_name = $request->province_name;
        $umkmData->city_id = $request->city_id;
        $umkmData->city_name = $request->city_name;
        $umkmData->subdistrict = $request->subdistrict;
        $umkmData->subdistrict_id = $request->subdistrict_id;
        $umkmData->village = $request->village;
        $umkmData->postalCode = $request->postalCode;
        $umkmData->ukmAddress = $request->ukmAddress;
        $umkmData->StoreSettingDone = 1;

        $umkmData->save();
        return ResponseFormatter::success('Data telah diupdate');

    }

    function setAvatar(Request $request) {
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }
        $image = $request->file('profile_photo');

        if($image) {
            $getImageName = imageResizer::ResizeImage($image, 'avatar', 'umkm-photo', 120, 120);
            $umkmData->profile_photo = $getImageName;
            $umkmData->save();
        
        }

        return ResponseFormatter::success('Sukses upload avatar');
    }

    public function businessSetting(Request $request){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }
        $umkmData->ownerName = $request->ownerName;
        $umkmData->BussinessFormType = $request->BussinessFormType;
        $umkmData->ownerPhoneNumber = $request->ownerPhoneNumber;
        $umkmData->businessStart = $request->businessStart;
        $umkmData->certificate = $request->certificate;
        $umkmData->certificateName = $request->certificateName;
        $umkmData->annualIncome = $request->annualIncome;
        $umkmData->businessStart = $request->businessStart;
        $umkmData->totalEmployee = $request->totalEmployee;
        $umkmData->isInterestedToJoinUmkmid = $request->isInterestedToJoinUmkmid;
        $umkmData->GeneralInfomrationDone = 1;

        $umkmData->save();
        return ResponseFormatter::success('Data telah diupdate');

    }

    public function login(Request $request){
        $credentials = request(['email', 'password']);
        $userCheck = umkm::where('email', $request->email)->get();

        if ($userCheck === null) {
            return ResponseFormatter::error(null, 'Email tidak terdaftar');
        }

        if (!$token = auth('umkm')->attempt($credentials)) {
            return response()->json(['error' => 'Data Tidak sesuai,silahkan cek kembali'], 401);
        }
        return ResponseFormatter::success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');
    }

    public function me()
    {
        $user = auth('umkm')->user();
    if($user === null){
            return response()->json(['error' => 'Invalid token'], 401);
    }

        return ResponseFormatter::success(['data' => $user], 'Berhasil login');
    }
    public function refresh()
    {
        return ResponseFormatter::success(['access_token' => auth('umkm')->refresh(), 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Refresh tokeun success');

   
    }
    public function addNpwp(Request $request){
        $user = auth('umkm')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }
        $umkmData->npwp_no = $request->npwp_no;
        $umkmData->documentSettingStatus =1;
        
        $umkmData->save();

        return ResponseFormatter::success('Berhasil update data');

    }

    function addNPWPPhoto(Request $request)
    {
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }
        $image = $request->file('npwp_photo');

        if ($image) {
            $getImageName = imageResizer::ResizeImage($image, 'npwp', 'umkm-photo', 120, 120);
            $umkmData->npwp_photo = $getImageName;
            $umkmData->documentSettingStatus = 1;
            $umkmData->save();
        }

        return ResponseFormatter::success('Sukses upload avatar');
    }

    function uploadKtp(Request $request) {
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }
        $image = $request->file('ktp_photo');

        if ($image) {
            $getImageName = imageResizer::ResizeImage($image, 'ktp', 'ktp-photo', 120, 120);
            $umkmData->ktp_photo = $getImageName;
            $umkmData->save();
        }

        return ResponseFormatter::success('Sukses upload avatar');
    }

    public function addBank(Request $request){
        $user = auth('umkm')->user();

        if ($user === null) {
            return ResponseFormatter::error('Please Login for continue ', 401);
        }

        $umkmData = umkm::where('id', $user['id'])->first();
        if ($umkmData === null) {
            return ResponseFormatter::error('Tidak dapat menemukan data ukm', 400);
        }

        $umkmData->bankAccountNumber = $request->bankAccountNumber;
        $umkmData->bankAccountName	 = $request->bankAccountName;
        $umkmData->bankAccountType	 = $request->bankAccountType;
        $umkmData->bankSettingStatus = 1;
        $umkmData->save();

        return ResponseFormatter::success('Sukses add bank data');


    }

    public function logout()
    {
        try {
            auth('umkm')->logout();
            // or Auth::logout();

            return response()->json([
                'error'   => false,
                'message' => trans('auth.logged_out')
            ]);
        } catch (TokenExpiredException $exception) {
            return response()->json([
                'error'   => true,
                'message' => trans('auth.token.expired')

            ], 401);
        } catch (TokenInvalidException $exception) {
            return response()->json([
                'error'   => true,
                'message' => trans('auth.token.invalid')
            ], 401);
        } catch (JWTException $exception) {
            return response()->json([
                'error'   => true,
                'message' => trans('auth.token.missing')
            ], 500);
        }
    }

    public function showUmkmStore($slug){
        $umkmProduct = umkm::where('slug', $slug)->with('product','product.images','product.umkm:id,ukmName,city_name')->select(['id','ukmName','province_name','city_name', 'profile_photo', 'description'])->first();

        return ResponseFormatter::success($umkmProduct,'Sukses ');
    }
    public function getUmkmByName($ukmName){
        $umkm = umkm::where('ukmName',$ukmName)->select('ukmName','city_name','city_id')->get();
        if($umkm === null){
            return ResponseFormatter::error('umkm tidak ditemukan');
        }

        return ResponseFormatter::success($umkm,'berhasil');

    }
    

}
