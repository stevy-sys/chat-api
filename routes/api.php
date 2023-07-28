<?php
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');
header('Referer-Policy: *');
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
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, PATCH, DELETE');
// header('Access-Control-Allow-Headers: Accept, Content-Type, X-Auth-Token, Origin, Authorization');

// Route::get('/user', function (Request $request) {
//     return auth()->user();
// })->middleware('auth:sanctum');

// Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/test', function () {
    return response()->json([
        'sataus' => 'ok'
    ]);
});
Route::get('/unauth',[AuthController::class,'notAuth'])->name('not-auth');
Route::any('/connexion',[AuthController::class,'login'])->name('post.login');
Route::post('/register',[AuthController::class,'register'])->name('register');

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/all-user',[ChatController::class,'allUsers'])->name('users.index');
    Route::get('/all-conversation',[ChatController::class,'allConversation'])->name('conversation.index');
    Route::get('/all-discussion/{idConversation}',[ChatController::class,'allDiscussion'])->name('conversation.show');
    Route::post('/send-message',[ChatController::class,'createMessage'])->name('message.store');
});
