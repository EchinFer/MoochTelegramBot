<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\TelegramUtil;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Templates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthTelegramController extends Controller
{
    public function loginBot()
    {
        $settings = new Settings();

        $appinfo = (new AppInfo);
        $appinfo = $appinfo->setApiId('20585216');
        $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        $templateSetting = new Templates;
        $templateSetting = $templateSetting->setHtmlTemplate('
            <!DOCTYPE html>
            <html>
                    <head>
                        <title>MadelineProto Test</title>
                    </head>
                    <body>
                        <h1>MadelineProto Test</h1>
                        <form method="POST" action="/madeline_proto/home">
                            %s
                            <button type="submit"/>Comenzar</button>
                            </form>
                            <p>%s</p>
                    </body>
            </html>
        ');

        $settings->setAppInfo($appinfo);
        $settings->setTemplates($templateSetting);

        $settings = $settings;

        $madelineProto = new \danog\MadelineProto\API('session.madeline.test', $settings); // The session will be serialized to session.madeline.example

        try {
            // return $madelineProto->start();

            $madelineProto->botLogin('6025075626:AAEMfLD54AnvBQ52vELtp-YMbLci8i_f9EA');
            $result = $madelineProto->messages->sendMessage(['peer' => '6084274322', 'message' => 'Hola Mundo']);
            var_dump($result);
        } catch (\Throwable $th) {
            var_dump($th);
        }
    }

    public function phoneLogin(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;
        $numeroTelefono = $request->numeroTelefono;

        $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
        $nombreSesion = TelegramUtil::formatearNombreSesion($cliente, $canalId);
        $madelineProto = new \danog\MadelineProto\API($nombreSesion, $settings);

        $jsonResponse = null;
        try {
            $response = $madelineProto->phoneLogin($numeroTelefono);
            $jsonResponse = response()->json([
                "status" => "success",
                "telegram_response" => $response
            ]);
        } catch (\Throwable $th) {
            // var_dump($th->getMessage());
            $jsonResponse = response()->json([
                "status" => "fail",
                "message" => $th->getMessage()
            ]);
        }

        return $jsonResponse;
    }

    public function completePhoneLogin(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;
        $codigoVerificacion = $request->codigoVerificacion;

        $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
        $nombreSesion = TelegramUtil::formatearNombreSesion($cliente, $canalId);
        $madelineProto = new \danog\MadelineProto\API($nombreSesion, $settings);

        $jsonResponse = null;
        try {
            $authorization = $madelineProto->completePhoneLogin($codigoVerificacion);

            $jsonResponse = response()->json([
                "status" => "success",
                "telegram_response" => $authorization["_"]
            ]);

            Log::info($nombreSesion, [
                "authorization" => $authorization
            ]);
        } catch (\Throwable $th) {
            $jsonResponse = response()->json([
                "status" => "fail",
                "message" => $th->getMessage()
            ]);
        }

        return $jsonResponse;
    }

    public function passwordPhoneLogin(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;
        $passwordTelefono = $request->passwordTelefono;

        $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
        $nombreSesion = TelegramUtil::formatearNombreSesion($cliente, $canalId);
        $madelineProto = new \danog\MadelineProto\API($nombreSesion, $settings);

        $jsonResponse = null;
        try {
            $authorization = $madelineProto->complete2falogin($passwordTelefono);

            $jsonResponse = response()->json([
                "status" => "success",
                "telegram_response" => $authorization["_"]
            ]);
        } catch (\Throwable $th) {
            // var_dump($th->getMessage());
            $jsonResponse = response()->json([
                "status" => "fail",
                "message" => $th->getMessage()
            ]);
        }

        return $jsonResponse;
    }
}
