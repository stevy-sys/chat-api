<?php
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');
header('Referer-Policy: *');
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::post('/test', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});

Route::get('/unauth',[AuthController::class,'notAuth'])->name('not-auth');
Route::post('/connexion',[AuthController::class,'login'])->name('post.login');
Route::post('/register',[AuthController::class,'register'])->name('register');

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/all-user',[ChatController::class,'allUsers'])->name('users.index');
    Route::get('/all-conversation',[ChatController::class,'allConversation'])->name('conversation.index');
    Route::get('/all-discussion/{idConversation}',[ChatController::class,'allDiscussion'])->name('conversation.show');
    Route::post('/send-message',[ChatController::class,'createMessage'])->name('message.store');
});

