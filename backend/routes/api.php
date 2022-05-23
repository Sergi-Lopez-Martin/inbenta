<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Middleware\InbentaAuth;

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

Route::middleware([InbentaAuth::class])->group(function(){
    Route::post('/chatbot/message', [ChatController::class, 'post_message']);
    Route::get('/chatbot/start_session', [ChatController::class, 'start_session']);
});