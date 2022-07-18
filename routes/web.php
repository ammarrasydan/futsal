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
    return view('dashboard');
})->middleware('auth', 'preventBackHistory');

Route::get('/user/new', function () {
    return view('newuser');
})->middleware('auth', 'preventBackHistory');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth', 'preventBackHistory');

Route::get('/profile/edit', function () {
    return view('editprofile');
})->middleware('auth', 'preventBackHistory');

Route::get('/user/edit/{id}', function ($id) {
    return view('edituser')->with('id', $id);
})->middleware('auth', 'preventBackHistory');

Route::get('/order/edit/{orderno}', function ($orderno) {
    return view('editorder')->with('orderno', $orderno);
})->middleware('auth', 'preventBackHistory');

Route::get('/users/list', function () {
    return view('listusers');
})->middleware('auth', 'preventBackHistory');

Route::get('/activity/logs', function () {
    return view('listactivitylogs');
})->middleware('auth', 'preventBackHistory');

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth', 'preventBackHistory');

Route::get('/orders/list', function () {
    return view('listorders');
})->middleware('auth', 'preventBackHistory');

Route::get('/settings/productdiscountrate', function () {
    return view('productdiscountrate');
})->middleware('auth', 'preventBackHistory');

Route::get('/ping', function () {
    return view('ping');
})->middleware('preventBackHistory');

Route::get('logout', 'Auth\LoginController@logout');

Route::get('/storage/app/uploads/{filename}', function ($filename) {
    $path = storage_path() . '/app/uploads/' . $filename;

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('avatar');

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "All cache were cleared.";
});

Route::get('/cache-all', function () {
    Artisan::call('config:cache');
    Artisan::call('view:cache');
    return "Config, route, view cached.";
});

Route::get('/storage/app/downloads/{filename}', function ($filename) {
    $path = storage_path() . '/app/downloads/' . $filename;

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('avatar');

Route::get('ajax', function () {
    return view('ajax');
});

// Booking Section

Route::get('/booking/new', function () {
    return view('addbooking');
})->middleware('auth', 'preventBackHistory');

Route::get('/booking/table/{id}', function ($id) {
    return view('tablebooking')->with('id', $id);
})->middleware('auth', 'preventBackHistory');

Route::get('/booking/list', function () {
    return view('listbooking');
})->middleware('auth', 'preventBackHistory');

////////////////////////////////////////////////////////

Route::post('/Login', 'AjaxController@Login');
Route::post('/ForgotPassword', 'AjaxController@ForgotPassword');
Route::post('/ResetPassword', 'AjaxController@ResetPassword');
Route::post('/PrepareProfile', 'AjaxController@PrepareProfile')->middleware('auth');
Route::post('/PrepareDashboard', 'AjaxController@PrepareDashboard')->middleware('auth');

Route::post('/GetUsers', 'AjaxController@GetUsers')->middleware('auth');
Route::post('/PrepareNewUser', 'AjaxController@PrepareNewUser')->middleware('auth');
Route::post('/NewUser', 'AjaxController@NewUser')->middleware('auth');
Route::post('/PrepareEditUser', 'AjaxController@PrepareEditUser')->middleware('auth');
Route::post('/EditUser', 'AjaxController@EditUser')->middleware('auth');
Route::post('/DeleteUser', 'AjaxController@DeleteUser')->middleware('auth');

Route::post('/GetOrders', 'AjaxController@GetOrders')->middleware('auth');
Route::post('/PrepareEditOrder', 'AjaxController@PrepareEditOrder')->middleware('auth');
Route::post('/DeleteOrder', 'AjaxController@DeleteOrder')->middleware('auth');

Route::post('/PrepareEditProfile', 'AjaxController@PrepareEditProfile')->middleware('auth');
Route::post('/ChangePassword', 'AjaxController@ChangePassword')->middleware('auth');

Route::post('/GetProductDiscountRate', 'AjaxController@GetProductDiscountRate')->middleware('auth');
Route::post('/EditProductPricePercentage', 'AjaxController@EditProductPricePercentage')->middleware('auth');

Route::post('/UploadImage1', 'AjaxController@UploadImage1')->middleware('auth');
Route::post('/UploadImage2', 'AjaxController@UploadImage2')->middleware('auth');
Route::post('/DeleteImage1', 'AjaxController@DeleteImage1')->middleware('auth');
Route::post('/DeleteImage2', 'AjaxController@DeleteImage2')->middleware('auth');

Route::post('/GetActivities', 'AjaxController@GetActivities')->middleware('auth');
Route::post('/GetBookings', 'AjaxController@GetBookings')->middleware('auth');
Route::post('/DeleteBooking', 'AjaxController@DeleteBooking')->middleware('auth');
Route::post('/GetBookingTable', 'AjaxController@GetBookingTable')->middleware('auth');
Route::post('/GetBookingData', 'AjaxController@GetBookingData')->middleware('auth');
Route::post('/EditBookingData', 'AjaxController@EditBookingData')->middleware('auth');
Route::post('/PrepareNewBooking', 'AjaxController@PrepareNewBooking')->middleware('auth');
Route::post('create','AjaxController@insert')->middleware('auth');
Route::get('booking/printbooking/{id}','AjaxController@index')->middleware('auth');

Auth::routes();
