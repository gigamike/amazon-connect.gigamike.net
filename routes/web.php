<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ContactUsController;

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
Route::resource('contactus', ContactUsController::class);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('logout', [UsersController::class, 'logout'])->name('logout');

Route::resource('users', UsersController::class);
Route::post('/ajaxIsCcpLoggedin', [UsersController::class, 'ajaxIsCcpLoggedin'])->name('ajaxIsCcpLoggedin');
Route::get('/windowOpenAmazonConnectStream', [UsersController::class, 'windowOpenAmazonConnectStream'])->name('windowOpenAmazonConnectStream');

Route::post('/ajaxAgentInit', [UsersController::class, 'ajaxAgentInit'])->name('ajaxAgentInit');
Route::post('/ajaxAgentStateChange', [UsersController::class, 'ajaxAgentStateChange'])->name('ajaxAgentStateChange');
Route::post('/ajaxAgentUpdateCurrentStatus', [UsersController::class, 'ajaxAgentUpdateCurrentStatus'])->name('ajaxAgentUpdateCurrentStatus');
Route::post('/ajaxAgentStatusLogout', [UsersController::class, 'ajaxAgentStatusLogout'])->name('ajaxAgentStatusLogout');
Route::post('/ajaxOpenNewWindow', [UsersController::class, 'ajaxOpenNewWindow'])->name('ajaxOpenNewWindow');
Route::post('/ajaxDisplayStatus', [UsersController::class, 'ajaxDisplayStatus'])->name('ajaxDisplayStatus');

Route::resource('customers', CustomersController::class);
