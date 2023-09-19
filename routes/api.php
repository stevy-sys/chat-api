<?php
// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
// header('Access-Control-Allow-Origin: *');
// header('Referer-Policy: *');
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::get('/user', function (Request $request) {
//     return auth()->user();
// })->middleware('auth:sanctum');

// Broadcast::routes(['middleware' => ['auth:sanctum']]);

