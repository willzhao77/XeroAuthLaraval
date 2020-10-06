<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XeroAuth\AuthorizationController;
use App\Http\Controllers\XeroAuth\CallBackController;
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



Route::get('authorization', [AuthorizationController::class, 'index']);
Route::get('callback', [CallBackController::class, 'index']);
