<?php

namespace App\Http\Controllers;

use App\Helper\imageResizer;
use App\Helper\ResponseFormatter;
use App\Models\umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class umkmController extends Controller
{
    public function register(Request $request ){
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'ukmName' => "required",
            'ownerName' => "required",
            'ownerPhoneNumber' => "required",
            'BussinessFormType' => "required",
            'businessStart' => "required",
            'ukmAddress' => "required",
            'city_name' => "required",
            'subdistrict' => "required",
            'postalCode' => "required",
            'certificate' => "required",
            'certificateName' => "required",
            'totalEmployee' => "required",
            'SocialMedia' => "required",
            'productSampleName' => "required",
            'productSampleCategory' => "required",
            'productSampleCapacity' => "required",
            'productSampleDescription' => "required",
            'productSamplePhoto' => "required",
            'annualIncome' => "required",
        ]);
        $userCheck = umkm::where('email', $request->email)->first();
        if ($userCheck !== null) {
            return ResponseFormatter::error(null, 'Email telah terdaftar');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }

        $umkmData = umkm::create($request->all());
        $image      = $request->file('productSamplePhoto');
        $fileName   = time() . '.' . $image->getClientOriginalExtension();
        $getImageName = imageResizer::ResizeImage($image,'product-sample','umkm-photo',120,120);

        $umkmData->password = bcrypt($request->password);
        $umkmData->productSamplePhoto = $getImageName;
        $umkmData->save(); 

        return ResponseFormatter::success($umkmData,'berhasil daftar');
        
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
        return ResponseFormatter::success(['access_token' => auth()->refresh(), 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Refresh tokeun success');

   
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

    

}
