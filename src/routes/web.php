<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\RoleController;
use \App\Http\Controllers\AuthController;
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

Route::view("/", "welcome");

Route::group(["middleware" => ["auth"]], function (){
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get("auth/logout", [AuthController::class, "logout"])->name("logout");
});

Route::group(["middleware" => ["guest"]], function (){
    Route::get("auth/login", [AuthController::class, "login"])->name("login");
    Route::post("auth/login", [AuthController::class, "authenticate"]);
});
