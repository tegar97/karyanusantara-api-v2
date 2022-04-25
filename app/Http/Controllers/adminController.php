<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\admin;
use App\Models\buyer;
use App\Models\transction;
use App\Models\umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Midtrans\Transaction;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class adminController extends Controller
{
    public function create(Request $request){
        $data = $request->only('name', 'email', 'password','code');

        if($request->code !== env("code_key")){
            return ResponseFormatter::error('invalid');
        }
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        $userCheck = admin::where('email', $request->email)->first();
        if ($userCheck !== null) {
            return ResponseFormatter::error(null, 'Email telah terdaftar');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
        }

        admin::create(['name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
    ]);





        //User created, return success response

        return ResponseFormatter::success('Register Berhasil');
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        $userCheck = admin::where('email', $request->email)->get();

        if ($userCheck === null) {
            return ResponseFormatter::error(null, 'Email tidak terdaftar');
        }
        if (!$token = auth('admin')->attempt($credentials)) {
            return ResponseFormatter::error(null, 'Data tidak sesuai ,silahkan coba kembali', 401);
        };
        $myTTL = 120;
        JWTAuth::factory()->setTTL($myTTL);

        // $cookie = cookie('token', $token, 60 * 24);
        return ResponseFormatter::success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');
    }
    public function me()
    {
        $user = auth('admin')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return ResponseFormatter::success(['data' => $user], 'Berhasil login');
    }

    public function refresh()
    {
        return ResponseFormatter::success(['access_token' => auth('admin')->refresh(), 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Refresh tokeun success');
    }

    public function  getUmkm(){
        $user = auth('admin')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        $umkm = umkm::with('transaction:umkm_id,invoice','product:id')->select('id','ukmName','ownerPhoneNumber')->get();

        return ResponseFormatter::success($umkm ,'Sukses');

    }
    public function  getUmkmDetail($id){
        $user = auth('admin')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        $umkm = umkm::with('transaction:umkm_id,invoice', 'product:id')->where('id',$id)->first();

        return ResponseFormatter::success($umkm ,'Sukses');

    }
    public function  GetUmkmTransaction($id, $status){
        $user = auth('admin')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        $umkm = transction::with('transactionItem.product:id,name,price,weight', 'transactionItem.product.images', 'umkm:id,ukmName', 'transactionItem.product:id,name,price','buyers:id,username_lkpp,name')->where('umkm_id',$id)->where('status', $status)->get();

        return ResponseFormatter::success($umkm ,'Sukses');

    }

    public function mitraSsoLogin(Request $request){
        $user = auth('admin')->user();
        if ($user === null) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        $email = $request->email;
        $key = $request->header('key');
        if($key == env('code_key')){
            $buyer = umkm::where('email',$email)->first();
            if($buyer === null){
                return ResponseFormatter::error('invalid email');
            }

            if (!$tokenLogin = auth('umkm')->login($buyer)) {
                return  response()->json([
                    "code" => 400,
                    "data" => null,
                    "message" => "auth failed",
                    "status" => false
                ]);
            };

            return ResponseFormatter::success($tokenLogin);

        }else{
            return ResponseFormatter::error('invalid key');

        }
    }
    
 
}
