<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'],function(){
    Route::get('/billing','BillingController@index')->name('billing');
    Route::get('/checkout/{plan_id}','BillingController@checkout')->name('checkout');
    Route::get('/cancel-plan','BillingController@cancelPlan')->name('cancel');
    Route::get('/resume-plan','BillingController@resumePlan')->name('resume');
    Route::post('/checkout/process','BillingController@process_checkout')->name('checkout.process');
    Route::get('payment-methods/default/{methodId}','PaymentMethodController@default')->name('payment-methods.default');
    Route::resource('payment-methods','PaymentMethodController');
});

Route::stripeWebhooks('stripe-webhook');
