<?php

use App\Http\Controllers\Api\Telegram\AuthTelegramController;
use App\Http\Controllers\MadelineProtoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/telegram/iniciarSesion', [AuthTelegramController::class, "iniciarSesion"]);
// Route::post('/madeline_proto/home', [MadelineProtoController::class, "home"]);

// Route::get('/madeline_proto/loginBot', [MadelineProtoController::class, "loginBot"]);
// Route::post('/madeline_proto/loginBot', [MadelineProtoController::class, "loginBot"]);





// Route::get('/madeline_proto/phoneLogin', [MadelineProtoController::class, "phoneLogin"]);
// Route::get('/madeline_proto/completePhoneLogin', [MadelineProtoController::class, "completePhoneLogin"]);

// Route::get('/madeline_proto/sendMessageFromUser', [MadelineProtoController::class, "sendMessageFromUser"]);

// Route::get('/madeline_proto/setWebhook', [MadelineProtoController::class, "setWebhook"]);

// Route::get('/madeline_proto/getContacts', [MadelineProtoController::class, "getContacts"]);

// Route::get('/madeline_proto/getId', [MadelineProtoController::class, "getId"]);

// Route::get('/madeline_proto/unsetEventHandler', [MadelineProtoController::class, "unsetEventHandler"]);

// Route::post('/madeline_proto/startHandlerLoop', [MadelineProtoController::class, "startHandlerLoop"]);






