<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    //所有商家
    Route::get('shops', 'ApiController@shops');
    //某个商家
    Route::get('shop', 'ApiController@shop');
    //短信验证
    Route::get('sms','ApiController@sms');
    //登录
    Route::post('login','ApiController@login');
    //注册
    Route::post('join','ApiController@join');
    //修改密码
    Route::post('change_pw','ApiController@change_pw');
    //添加地址
    Route::post('add_address','ApiController@add_address');
    //地址列表
    Route::get('address_list','ApiController@address_list');
    //详细地址
    Route::get('address','ApiController@address');
    //修改地址
    Route::post('edit_address','ApiController@edit_address');
    //加入购物车
    Route::post('add_cart','ApiController@add_cart');
    //购物车内列表
    Route::get('cart','ApiController@cart');
    //忘记密码
    Route::post('forget_password','ApiController@forget_password');
    //添加订单
    Route::post('add_order','ApiController@add_order');
    //查看订单
    Route::get('order','ApiController@order');
    //订单列表
    Route::get('order_list','ApiController@order_list');

});

