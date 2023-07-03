<?php

use App\Http\Controllers\Api\Telegram\AuthTelegramController;
use App\Http\Controllers\Api\Telegram\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Telegram;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Database\Mysql;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('webhook-user', function (Request $request) {
    $requestData = $request->all();

    $botApiKey = env("TELEGRAM_API_TOKEN");
    $botUserName = "echin_fer_bot";
    Log::info(
        "Telegram Data USER",
        $requestData
    );

    try {
        // // Create Telegram API object
        // $telegram = new Longman\TelegramBot\Telegram($botApiKey, $botUserName);

        $buttons = [
            [
                "label" => "Btn1",
                "replyData" => "btn-1"
            ],
            [
                "label" => "Btn2",
                "replyData" => "btn-2"
            ],
            [
                "label" => "Btn3",
                "replyData" => "btn-3"
            ]
        ];

        // $responseKeyboardMessage = Telegram::sendInlineKeyboardMessage("6084274322", "Teclado de ejemplo", $buttons);
        //
        // var_dump($responseKeyboardMessage);
        // Log::info(
        //     "Response Keyboard Message",
        //     $responseKeyboardMessage
        // );

        // return response()->json([
        //     'status' => 'success',
        //     'msg' => 'Enviado correctamente',
        // ], 200);

        // $result = Longman\TelegramBot\Request::sendMessage([
        //     'chat_id' => "6084274322",
        //     'text'    => 'Hola soy Neeko ward',
        // ]);

        // return $telegram->handle();
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // Silence is golden!
        // log telegram errors
        // echo $e->getMessage();
        Log::error(
            "Telegram Error",
            [$e->getMessage()]
        );
        // $a = $e;
        return response()->json([
            'status' => 'fail',
            'error' =>  $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 'success',
        'msg' => 'Recibido correctamente'
    ], 200);
    // return $command->execute();
});


Route::post('webhook', function (Request $request) {
    $requestData = $request->all();

    $botApiKey = env("TELEGRAM_API_TOKEN");
    $botUserName = "echin_fer_bot";
    Log::info(
        "Telegram Data",
        $requestData
    );

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($botApiKey, $botUserName);

        $buttons = [
            [
                "label" => "Btn1",
                "replyData" => "btn-1"
            ],
            [
                "label" => "Btn2",
                "replyData" => "btn-2"
            ],
            [
                "label" => "Btn3",
                "replyData" => "btn-3"
            ]
        ];

        // $responseKeyboardMessage = Telegram::sendInlineKeyboardMessage("6084274322", "Teclado de ejemplo", $buttons);
        //
        // var_dump($responseKeyboardMessage);
        // Log::info(
        //     "Response Keyboard Message",
        //     $responseKeyboardMessage
        // );

        // return response()->json([
        //     'status' => 'success',
        //     'msg' => 'Enviado correctamente',
        // ], 200);

        // $result = Longman\TelegramBot\Request::sendMessage([
        //     'chat_id' => "6084274322",
        //     'text'    => 'Hola soy Neeko ward',
        // ]);

        return $telegram->handle();
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // Silence is golden!
        // log telegram errors
        // echo $e->getMessage();
        Log::error(
            "Telegram Error",
            [$e->getMessage()]
        );
        // $a = $e;
        return response()->json([
            'status' => 'fail',
            'error' =>  $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 'success',
        'msg' => 'Recibido correctamente'
    ], 200);
    // return $command->execute();
});


Route::post('sendMessage', function (Request $request) {
    try {

        // $API = new \danog\MadelineProto\API('session.madeline', $settings);
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // Silence is golden!
        // log telegram errors
        // echo $e->getMessage();
        Log::error(
            "Telegram Error",
            [$e->getMessage()]
        );

        $settings = (new AppInfo())->setApiId('20585216');


        // $MadelineProto->updateSettings($settings);

        // $a = $e;
        return response()->json([
            'status' => 'fail',
            'error' =>  $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 'success',
        'msg' => 'Enviado correctamente'
    ], 200);
});





Route::prefix('telegram')->group(function() {

    //AUTHENTICATION
    Route::post('/iniciarSesion', [AuthTelegramController::class, "iniciarSesion"]);
    Route::post('/iniciarSesionTelefono', [AuthTelegramController::class, "iniciarSesionTelefono"]);
    Route::post('/completarIniciarSesionTelefono', [AuthTelegramController::class, "completarIniciarSesionTelefono"]);
    Route::post('/completar2faSesion', [AuthTelegramController::class, "completar2faSesion"]);

    //START HANDLER EVENT
    Route::post('/iniciarManejadorEventosMtpro', [TelegramController::class, "iniciarManejadorEventosMtpro"]);


    //COMMONS
    Route::get('/obtenerContactos', [TelegramController::class, "obtenerContactos"]);
    Route::post('/enviarMensaje', [TelegramController::class, "enviarMensaje"]);

});