<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnonymousController;
use App\Http\Controllers\AnonymousMessageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomMessageController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

// RoomMessageController
 


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

Route::get('voicenotes/{filename}', function ($filename) {
    $path = public_path('storage/voicenotes/'. $filename);

    if(!file_exists($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return Response::make($file, 200, [
        'Content-Type'=>$type,
        // 'Content-Disposition'=> 'inline, filename="'. $filename . '"',
    ]);

})->name('voicenote.get');

Route::group(['middleware'=> ['auth:sanctum']], function () {
    Route::post('/createLink', [AnonymousController::class, 'createLink']);
    Route::get('/getLink', [AnonymousController::class, 'getLink']);
    Route::get('/linkName/{id}', [AnonymousController::class, 'linkName']);

    
    Route::get('/allMyLinks', [AnonymousController::class, 'allMyLinks']);
    Route::get('/goodReviews', [AnonymousController::class, 'goodReviews']);

    Route::post('/message', [AnonymousMessageController::class, 'sendMessage']);
    Route::get('/message/{anon_id}', [AnonymousMessageController::class, 'getMessages']);
    

    //rooms
    Route::post('/createRooom', [RoomController::class, 'createRoom']);
    Route::get('/recievedRooms', [RoomController::class, 'getreceivedRooms']);
    Route::get('/sentRooms', [RoomController::class, 'getsentRooms']);
    Route::get('/checkRoom/{userid}', [RoomController::class, 'checkRoom']);
    Route::put('/blockUser', [RoomController::class, 'blockUser']);
    Route::put('/reportUser', [RoomController::class, 'reportUser']);
    Route::put('/allowLinks', [RoomController::class, 'allowLinks']);
    Route::put('/revealProfile', [RoomController::class, 'revealProfile']);

    
    
    
    // chat
    Route::post('/sendMessage', [RoomMessageController::class, 'send']);
    Route::post('/sendReply', [RoomMessageController::class, 'reply']);
    Route::get('/getMessage/{roomid}', [RoomMessageController::class, 'getMessages']);
    Route::get('/read/{roomid}', [RoomMessageController::class, 'read']);
    Route::post('/voiceNote', [RoomMessageController::class, 'voiceNote']);


    // 
    // tutorial
    Route::put('/tutorial', [AuthController::class, 'tutorial']);
    Route::get('/userInfo', [AuthController::class, 'userInfo']);
    Route::get('/changeAvatar', [AuthController::class, 'changeAvatar']);
    Route::post('/addAvatar', [AuthController::class, 'addAvatar']);
    Route::post('/inviteUser', [AuthController::class, 'inviteUser']);



    // allMyLinks
    Route::post('/logout', [AuthController::class, 'Logout']);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
