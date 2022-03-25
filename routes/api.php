<?php

use App\Http\Controllers\buyerAddressController;
use App\Http\Controllers\buyerController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\productController;
use App\Http\Controllers\productGalleryController;
use App\Http\Controllers\rajaOngkirController;
use App\Http\Controllers\subCategoryController;
use App\Http\Controllers\umkmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function($router) {
    Route::post('/register',[buyerController::class,'register']);
    Route::post('/login',[buyerController::class,'login']);
    Route::get('/me',[buyerController::class,'me']);
    Route::post('/refresh',[buyerController::class,'refresh']);
    Route::post('/update', [buyerController::class, 'update']);
    Route::post('/changePassword', [buyerController::class, 'changePassword']);



    //Api umkm
    Route::post('/umkm/register', [umkmController::class, 'register']);
    Route::post('/umkm/login', [umkmController::class, 'login']);
    Route::post('/umkm/me', [umkmController::class, 'me']);
    Route::post('/umkm/logout', [umkmController::class, 'logout']);
    Route::post('/umkm/refresh', [umkmController::class, 'refresh']);

    

});

//Api Buyer Address
Route::post('/buyerAddress',[buyerAddressController::class, 'create']);
Route::post('/buyerAddress/delete/{id}',[buyerAddressController::class, 'destroy']);
Route::post('/buyerAddress/update/{id}',[buyerAddressController::class, 'update']);

//Api image upload
Route::post('/product/image',[productGalleryController::class, 'create']);

//Api category

Route::get('/category',[categoryController::class,'view']);
Route::get('/category/get-product-by-category',[categoryController::class, 'GetProductByCategory']);
Route::get('/category/{id}',[categoryController::class,'detail']);
Route::post('/category',[categoryController::class,'create']);
Route::delete('/category/{id}',[categoryController::class, 'destroy']);
Route::post('/category/{id}',[categoryController::class, 'update']);

//Api subCategory
Route::post('/subCategory',[subCategoryController::class, 'create']);
Route::delete('/subCategory/{id}',[subCategoryController::class, 'destroy']);
Route::put('/subCategory/{id}',[subCategoryController::class, 'update']);

Route::get('/umkm/product', [productController::class, 'view']);
Route::post('/umkm/product', [productController::class, 'create']);
Route::get('/umkm/product/{slug}', [productController::class, 'detail']);

Route::post('/orders', [orderController::class, 'NewOrder']);

//Raja ongkir

Route::get('/province',[rajaOngkirController::class, 'getProvince']);
Route::get('/cities',[rajaOngkirController::class, 'getCity']);
Route::get('/subdistricts',[rajaOngkirController::class, 'getsubdistricts']);
Route::get('/fastsearch',[rajaOngkirController::class, 'fastSearch']);
Route::post('/cekongkir', [rajaOngkirController::class, 'cekOngkir']);
Route::get('/getLowerOngkir', [rajaOngkirController::class, 'getLowerOngkir']);


