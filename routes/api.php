<?php
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



Route::get('/user', function (Request $request) {
    return auth()->user();
})->middleware('auth:sanctum');



Route::get('/test', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});

Route::get('/unauth',[AuthController::class,'notAuth'])->name('not-auth');
Route::post('/connexion',[AuthController::class,'login'])->name('post.login');
Route::post('/register',[AuthController::class,'register'])->name('register');
Route::get('/deconnect',[AuthController::class,'deconnect'])->name('deconnect')->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/all-user',[ChatController::class,'allUsers'])->name('users.index');
    Route::get('/all-conversation',[ChatController::class,'allConversation'])->name('conversation.index');
    Route::get('/all-discussion/{idConversation}',[ChatController::class,'allDiscussion'])->name('conversation.show');
    Route::post('/send-message',[ChatController::class,'createMessage'])->name('message.store');
});
Broadcast::routes(['middleware' => ['auth:sanctum']]);
