<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use JWTAuth;

use App\Models\buyer;
use App\Models\buyerAddress;
use App\Models\cart;
use GrahamCampbell\ResultType\Success;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class buyerController extends Controller
{
    
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password','phoneNumber');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'phoneNumber' => "required"
        ]);
        $userCheck = Buyer::where('email',$request->email)->first();
        if ($userCheck !== null) {
            return ResponseFormatter::error(null, 'Email telah terdaftar');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors());
        }
        
        

        //Request is valid, create new user
        $user = buyer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phoneNumber' => $request->phoneNumber,
            
        ]);

        cart::create([
            'total' => 0,
            'buyers_id' => $user->id,
        
        ]);



        //User created, return success response

        return ResponseFormatter::success($user,'Register Berhasil');
       
    }


    public function login(Request $request) 
    {
        $credentials = request(['email','password']);
        $myTTL =1500;
        FacadesJWTAuth::factory()->setTTL($myTTL);
        $userCheck = Buyer::where('email', $request->email)->get();

        if($userCheck === null) {
            return ResponseFormatter::error(null, 'Email tidak terdaftar');

        }
        if (!$token = auth('api')->attempt($credentials)) {
            return ResponseFormatter::error(null,'Data tidak sesuai ,silahkan coba kembali',401);
        };

        // $cookie = cookie('token',$token,60*24);
        return ResponseFormatter::success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60],'Berhasil login');
    }
 
    public function logout()
    {
        auth()->logout();
        
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        $user = auth('api')->user();
        $address = buyerAddress::where('buyers_id','=',$user['id'])->get();
   

        return ResponseFormatter::success(['data' => $user,'address' => $address], 'Berhasil login');
       
    }

    public function update(Request $request){

        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $user = buyer::find($checkUser['id']);


        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phoneNumber' =>$request->phoneNumber
        ]);

        return ResponseFormatter::success('Success Update');


    }

    public function changePassword(Request $request) {
        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $user = buyer::find($checkUser['id']);
     

        if (!Hash::check($request->old_password, $user['password'])) {
            return ResponseFormatter::error('Gagal update password');
        };
        
        
        $user->update([
            'password' => bcrypt($request->new_password),
          
        ]);

        return ResponseFormatter::success('Sukses update password');
    }
    public function refresh()
    {
     return ResponseFormatter::success(['access_token' => auth()->refresh(), 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');

    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token, 'token_type' => 'bearer','expires_in' => auth()->factory()->getTTL() * 60


        ]);
    }

}
