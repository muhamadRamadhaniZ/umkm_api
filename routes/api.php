<?php

use App\Http\Controllers\API\BeritaController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
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
Route::post('/berita', [BeritaController::class, 'store']);


// USER Route
Route::post('/user', [UserController::class, 'register']);
Route::post('/user/update', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::get('/user', [UserController::class, 'fetch'])->middleware('auth:sanctum');
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/login', [UserController::class, 'login']);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
