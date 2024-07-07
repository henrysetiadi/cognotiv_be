<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::controller(RegisterController::class)->group(function(){
//     Route::post('register', 'register');
//     Route::post('login', 'login');
//     Route::post('logout', 'logout');
// });

// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);

//     return ['token' => $token->plainTextToken];
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/login', [RegisterController::class, 'login'])->name('login');
    Route::get('/logout', [RegisterController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [RegisterController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::get('/me', [RegisterController::class, 'me'])->middleware('auth:api')->name('me');

    Route::post('/submitPost', [PostController::class, 'submitPost'])->middleware('auth:api')->name('submitPost');
});



Route::get('/welcomePage', [App\Http\Controllers\PostController::class, 'fetchAllContent']);

Route::post('/submitPost', [App\Http\Controllers\PostController::class, 'submitPost']);
Route::get('/content', [App\Http\Controllers\PostController::class, 'fetchContent']);
Route::get('/getContent', [App\Http\Controllers\PostController::class, 'getDataContentById']);
Route::post('/editContent', [App\Http\Controllers\PostController::class, 'editContent']);
Route::post('/deleteContent', [App\Http\Controllers\PostController::class, 'deleteContent']);

Route::post('/submitComment', [App\Http\Controllers\CommentController::class, 'submitComment']);
Route::get('/comment', [App\Http\Controllers\CommentController::class, 'fetchComment']);
Route::post('/editComment', [App\Http\Controllers\CommentController::class, 'editComment']);
Route::post('/deleteComment', [App\Http\Controllers\CommentController::class, 'deleteComment']);

