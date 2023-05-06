<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnonymousController;
use App\Http\Controllers\AnonymousMessageController;

 


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

Route::get('/index', function () {
    return 'johnny drill22';
});



Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);

Route::group(['middleware'=> ['auth:sanctum']], function () {
    Route::post('/createLink', [AnonymousController::class, 'createLink']);
    Route::get('/allMyLinks', [AnonymousController::class, 'allMyLinks']);
    Route::get('/goodReviews', [AnonymousController::class, 'goodReviews']);

    Route::post('/message', [AnonymousMessageController::class, 'sendMessage']);
    Route::get('/message/{anon_id}', [AnonymousMessageController::class, 'getMessages']);
    
    
    // allMyLinks
    Route::post('/logout', [AuthController::class, 'Logout']);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
