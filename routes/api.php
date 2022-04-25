<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\buyerAddressController;
use App\Http\Controllers\buyerController;
use App\Http\Controllers\cartController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\payment_gateway_controller;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\productController;
use App\Http\Controllers\productGalleryController;
use App\Http\Controllers\rajaOngkirController;
use App\Http\Controllers\reviewsController;
use App\Http\Controllers\subCategoryController;
use App\Http\Controllers\umkmController;
use App\Http\Controllers\transctionController;
use App\Http\Controllers\transferLogController;
use App\Http\Controllers\webHookController;
use App\Models\review;
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
    Route::post('/checklkpp/{token}', [buyerController::class, 'checklkpp']);
    Route::post('/sso', [buyerController::class, 'loginsso']);



    //Api umkm
    Route::post('/umkm/register', [umkmController::class, 'register']);
    Route::post('/umkm/login', [umkmController::class, 'login']);
    Route::get('/umkm/me', [umkmController::class, 'me']);
    Route::post('/umkm/logout', [umkmController::class, 'logout']);
    Route::get('/umkm/refresh', [umkmController::class, 'refresh']);
    Route::put('/umkm/update', [umkmController::class, 'update']);
    Route::post('/umkm/storeEdit', [umkmController::class, 'storeEdit']);
    Route::post('/umkm/avatarUpdate', [umkmController::class, 'setAvatar']);
    Route::post('/umkm/businessSetting', [umkmController::class, 'businessSetting']);

    Route::post('/admin/register', [adminController::class, 'create']);
    Route::post('/admin/login', [adminController::class, 'login']);
    Route::get('/admin/me', [adminController::class, 'me']);
    Route::get('/admin/refresh', [adminController::class, 'refresh']);

    

});

//Api Buyer Address
Route::post('/buyerAddress',[buyerAddressController::class, 'create']);
Route::post('/buyerAddress/delete/{id}',[buyerAddressController::class, 'destroy']);
Route::post('/buyerAddress/update/{id}',[buyerAddressController::class, 'update']);

//Api image upload
Route::post('/product/image',[productGalleryController::class, 'create']);
Route::post('/product/Singleimage',[productGalleryController::class, 'singleUpload']);
Route::get('/products',[productController::class, 'filterCategory']);

//Api category

Route::get('/category',[categoryController::class,'view']);
Route::get('/category/get-product-by-category',[categoryController::class, 'GetProductByCategory']);
Route::get('/category/{id}',[categoryController::class,'detail']);
Route::post('/category',[categoryController::class,'create']);
Route::post('/uploadIconCategory/{id}',[categoryController::class, 'uploadIcon']);
Route::delete('/category/{id}',[categoryController::class, 'destroy']);
Route::post('/category/{id}',[categoryController::class, 'update']);
Route::get('/categories',[categoryController::class, 'getCategoriesWithSub']);
Route::get('/categoriestMain',[categoryController::class, 'getCategoryAndSubcategory']);

//Api subCategory
Route::post('/subCategory',[subCategoryController::class, 'create']);
Route::get('/subCategory',[subCategoryController::class, 'get']);
Route::delete('/subCategory/{id}',[subCategoryController::class, 'destroy']);
Route::post('/subCategory/{id}',[subCategoryController::class, 'update']);
Route::get('/getSubCategoryByCategory/{id}',[subCategoryController::class, 'getSubCategoryByCategory']);

Route::get('/umkm/product', [productController::class, 'view']);
Route::get('/umkm/myproducts', [productController::class, 'myproducts']);
Route::get('/umkm/underreview', [productController::class, 'underReviewProduct']);
Route::put('/umkm/product/updatestatus/{id}', [productController::class, 'updateStatus']);
Route::post('/umkm/product', [productController::class, 'create']);
Route::get('/umkm/product/main', [productController::class, 'getMainProduct']);
Route::get('/umkm/product/{slug}', [productController::class, 'detail']);
Route::get('/umkm/{slug}', [umkmController::class, 'showUmkmStore']);
Route::get('/umkm/getCity/{ukmName}', [umkmController::class, 'getUmkmByName']);
Route::post('/umkm/uploadKtp', [umkmController::class, 'uploadKtp']);
Route::post('/umkm/addNPWPPhoto', [umkmController::class, 'addNPWPPhoto']);
Route::post('/umkm/addNpwp', [umkmController::class, 'addNpwp']);
Route::post('/umkm/addBank', [umkmController::class, 'addBank']);
Route::put('/umkm/product/updateStock', [productController::class, 'updateStock']);

Route::post('/orders/mandiribill', [orderController::class, 'orderMandiriBill']);
Route::post('/orders/indomaret', [orderController::class, 'orderIndomaret']);
Route::post('/orders/VirtualAccount', [orderController::class, 'VirtualAccount']);

//Raja ongkir

Route::get('/province',[rajaOngkirController::class, 'getProvince']);
Route::get('/cities',[rajaOngkirController::class, 'getCity']);
Route::get('/subdistricts',[rajaOngkirController::class, 'getsubdistricts']);
Route::get('/fastsearch',[rajaOngkirController::class, 'fastSearch']);
Route::post('/cekongkir', [rajaOngkirController::class, 'cekOngkir']);
Route::get('/getLowerOngkir', [rajaOngkirController::class, 'getLowerOngkir']);


//Courier Service
Route::post('/courier',[CourierController::class, 'addCourier']);
Route::post('/subCourier',[CourierController::class, 'addSubServiceCourier']);
Route::post('/courierSettingUmkm',[CourierController::class, 'settingCouriersUmkm']);
Route::get('/courier', [CourierController::class, 'getCourier']);
Route::get('/courier/my', [CourierController::class, 'getMyCourier']);


//Reviews
Route::post('/reviews',[reviewsController::class,'create']);


//Cart
Route::post('/cart',[cartController::class,'create']);
Route::post('/cart/update',[cartController::class,'update']);
Route::get('/cart',[cartController::class, 'GetMyCart']);
Route::delete('/cart/{id}',[cartController::class, 'deleteItem']);

//Gateway

Route::post('/gateway',[payment_gateway_controller::class,'create']);
Route::get('/gateway',[payment_gateway_controller::class,'view']);


Route::get('/payment/{code}', [paymentController::class, 'get']);
Route::get('/payment', [paymentController::class, 'all']);
Route::get('/payment/detail/{code}', [paymentController::class, 'detail']);
Route::get('/payment/status/success', [paymentController::class, 'getHistory']);


Route::get('/transaction/code/{status}', [transctionController::class, 'getBuyerTransaction']);
Route::get('/transaction/seller', [transctionController::class, 'getSellerTransaction']);
Route::get('/transaction/seller/{code}', [transctionController::class, 'getSellerTransaction']);
Route::post('/transaction/ComplateTheOrder', [transctionController::class, 'ComplateTheOrder']);


Route::post('/webhook', [webHookController::class, 'midtransHandler']);

Route::post('/sendResi', [transctionController::class, 'sendResi']);
Route::post('/track', [transctionController::class, 'track']);
Route::get('/admin/product/{slug}', [productController::class, 'detailProductAdminAccess']);
Route::put('/admin/product/updatestatus/{id}', [productController::class, 'AcceptOrReject']);
Route::put('/admin/product/addMainProduct/{id}', [productController::class, 'addMainProduct']);

Route::post('/transferLog', [transferLogController::class, 'create']);
Route::get('/umkm/history/salesHistory', [transferLogController::class, 'getHistory']);
Route::get('/getUmkm', [adminController::class, 'getUmkm']);
Route::get('/getUmkm/{id}', [adminController::class, 'getUmkmDetail']);
Route::get('/getUmkmTransaction/{id}/{status}', [adminController::class, 'GetUmkmTransaction']);
Route::post('/admin/umkm/sso', [adminController::class, 'mitraSsoLogin']);
