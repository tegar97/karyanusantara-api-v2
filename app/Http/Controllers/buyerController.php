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
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;

class buyerController extends Controller
{

    public function loginsso(Request $request){

        $XID = $request->header('X-Client-Id');
        $XSECRET = $request->header('X-Client-Secret');
        if($XID  == env('X_Client_Id') && $XSECRET == env('X_Client_Secret')){
            $userToken = $request->token;

            $tokenBearer = 'Bearer ' . $userToken;

            $response = Http::withHeaders(['Authorization' => $tokenBearer])->post(env('TOKODARING_DEV'));
            if($response['status'] == true){
                $getEmail = $response['data']['email'];
                $checkIfExitst = buyer::where('email', $getEmail)->first();

                if ($checkIfExitst !== null) {
                    //Login

                    $myTTL = 1500;
                    FacadesJWTAuth::factory()->setTTL($myTTL);
                    $credentials = ['email' => $response['data']['email'], 'password' => $response['data']['email'] . $response['data']['userName']];

                    if (!$tokenLogin = auth('api')->claims(['token_lkpp' => $userToken])->attempt($credentials)) {
                        return  response()->json([
                            "code" => 400,
                            "data" => null,
                            "message" => "auth failed",
                            "status" => false
                        ]);
                    };
                    return response()->json([
                        "code" => 200,
                        "data" => [
                            "token" => $tokenLogin
                        ],
                        "message" => null,
                        "status" => true
                    ]);
                }else{
                    //Register
                    $user = buyer::create([
                        'name' =>
                        $response['data']['realName'],
                        'email' =>  $response['data']['email'],
                        'password' => bcrypt($response['data']['email'] . $response['data']['userName']),
                        'phoneNumber' =>  $response['data']['phone'],
                        'lkppRole' => $response['data']['role'],
                        'username_lkpp' => $response['data']['userName'],
                        'namaInstansi' => $response['data']['namaInstansi'],
                        'namaSatker' => $response['data']['namaSatker'],

                    ]);

                    $myTTL = 1500;
                    FacadesJWTAuth::factory()->setTTL($myTTL);
                    $credentials = ['email' => $user['email'], 'password' => $response['data']['email'] . $response['data']['userName']];



                    if (!$tokenLogin = auth('api')->claims(['token_lkpp' => $userToken])->attempt($credentials)) {
                         return  response()->json([
                            "code" => 400,
                            "data" => null,
                            "message" => "auth failed",
                            "status" => false
                        ]);
                    };

                    return response()->json([
                        "code" => 200,
                        "data" => [
                            "token" => $tokenLogin
                        ],
                        "message" => null,
                        "status" => true
                    ]);
                }
            }else{
                return  response()->json([
                    "code" => 400,
                    "data" => null,
                    "message" => "token  invalid",
                    "status" => false
                ]);
            }
        }else{
            return  response()->json([
                "code" => 400,
                "data" => null,
                "message" => "invalid   client id or client secrent",
                "status" => false
            ]);
        }
        

    }
    public function checklkpp(Request $request, $token)
    {
        $tokenBearer = 'Bearer ' . $token;
        $response = Http::withHeaders(['Authorization' => $tokenBearer])->post('https://dev-tokodaring-api.lkpp.go.id/uma/sso/auth');



        if ($response['status'] == true) {
            $getEmail = $response['data']['email'];
            $checkIfExitst = buyer::where('email', $getEmail)->first();

            if ($checkIfExitst !== null) {
                //Login

                $myTTL = 1500;
                FacadesJWTAuth::factory()->setTTL($myTTL);
                $credentials = ['email' => $response['data']['email'], 'password' => $response['data']['email'] . $response['data']['userName']];

                $userCheck = Buyer::where('email', $response['data']['email'])->get();
                if ($userCheck === null) {
                    return ResponseFormatter::error(null, 'Email tidak terdaftar');
                }

                if (!$tokenLogin = auth('api')->attempt($credentials)) {
                    return ResponseFormatter::error(null, 'Data tidak sesuai ,silahkan coba kembali', 401);
                };
                return ResponseFormatter::success(['access_token' => $tokenLogin, 'lkkp_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');

            } else {
                //Register
                $user = buyer::create([
                    'name' =>
                    $response['data']['realName'],
                    'email' =>  $response['data']['email'],
                    'password' => bcrypt($response['data']['email'].$response['data']['userName']),
                    'phoneNumber' =>  $response['data']['phone'],

                ]);

                $myTTL = 1500;
                FacadesJWTAuth::factory()->setTTL($myTTL);
                $credentials = ['email' => $user['email'], 'password' => $response['data']['email'].$response['data']['userName']];

             
               
                if (!$tokenLogin = auth('api')->attempt($credentials)) {
                    return ResponseFormatter::error(null, 'Data tidak sesuai ,silahkan coba kembali', 401);
                };
                return ResponseFormatter::success(['access_token' => $tokenLogin, 'lkkp_token' => $token,'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');
            }
        } else {
            return ResponseFormatter::error('Token is invalid');
        }



    }

    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password', 'phoneNumber');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'phoneNumber' => "required"
        ]);
        $userCheck = Buyer::where('email', $request->email)->first();
        if ($userCheck !== null) {
            return ResponseFormatter::error(null, 'Email telah terdaftar');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseFormatter::error(null, $validator->errors());
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

        return ResponseFormatter::success($user, 'Register Berhasil');
    }


    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        $myTTL = 1500;
        FacadesJWTAuth::factory()->setTTL($myTTL);
        $userCheck = Buyer::where('email', $request->email)->get();

        if ($userCheck === null) {
            return ResponseFormatter::error(null, 'Email tidak terdaftar');
        }
        if (!$token = auth('api')->attempt($credentials)) {
            return ResponseFormatter::error(null, 'Data tidak sesuai ,silahkan coba kembali', 401);
        };

        // $cookie = cookie('token',$token,60*24);
        return ResponseFormatter::success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60], 'Berhasil login');
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {

        try{
            $user = auth('api')->user();
            if($user === null){
               return ResponseFormatter::error('Invalid token');
            }
            $address = buyerAddress::where('buyers_id', '=', $user['id'])->get();


            return ResponseFormatter::success(['data' => $user, 'address' => $address], 'Berhasil login');
        }catch(JWTException $e){
            return ResponseFormatter::error('Invalid token');
        }
       
    
    }

    public function update(Request $request)
    {

        $checkUser =   auth('api')->user();
        if (!$checkUser) {
            return ResponseFormatter::error($data = null, 'Please login ');
        };
        $user = buyer::find($checkUser['id']);


        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber
        ]);

        return ResponseFormatter::success('Success Update');
    }

    public function changePassword(Request $request)
    {
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
            'access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60


        ]);
    }
}
