<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\TelegramEventHandler;
use App\Telegram\TelegramUtil;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function iniciarSesion($cliente, $canalId, $fullPath){
        $nombreSesion = TelegramUtil::obtenerNombreSesion($cliente, $canalId, $fullPath);
        $configuraciones = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);

        TelegramEventHandler::startAndLoop($nombreSesion, $configuraciones);
        // $madelineProtos = new API($nombreSesion, $configuraciones);
        // $madelineProtos->start();
    }
    public function iniciarManejadorEventosMtpro($fullPath = null)
    {
        $clientes = [
            "telefuturo" => ["canal_1"]
        ];
        try {

            $sesiones = [];

            foreach ($clientes as $cliente => $canales) {
                foreach ($canales as $canalId) {
                    $nombreSesion = TelegramUtil::obtenerNombreSesion($cliente, $canalId, $fullPath);
                    $configuraciones = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
                    $sesiones[] = ["nombreSesion" => $nombreSesion, "configuraciones" => $configuraciones];
                }
            }
            $nombreSesion = TelegramUtil::obtenerNombreSesion($cliente, $canalId, $fullPath);

            $madelineProtos = [];
            foreach ($sesiones as $sesion) {
                var_dump($sesion);
                $madelineProtos[] = new API($sesion["nombreSesion"], $sesion["configuraciones"]);
            }


            return;
            $session = $sesiones[0]["nombreSesion"];
            $settings = $sesiones[0]["configuraciones"];

            // TelegramEventHandler::startAndLoop($session, $settings);
            API::startAndLoopMulti($madelineProtos, TelegramEventHandler::class);
        } catch (\Throwable $th) {
            echo $th->getMessage();
            // $jsonResponse = [
            //     "status" => "fail",
            //     "telegram_response" => $th->getMessage()
            // ];
            return false;
        }

        // return response()->json($jsonResponse);
        // return json_encode($jsonResponse);
    }

    public function obtenerUltimosMensajes()
    {
    }

    public function enviarMensaje(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;
        $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
        $nombreSesion = TelegramUtil::obtenerNombreSesion($cliente, $canalId);
        $madelineProto = new \danog\MadelineProto\API($nombreSesion, $settings);

        try {
            $result = $madelineProto->messages->sendMessage(['peer' => '@msoveja', 'message' => 'Hola, mensaje de prueba :)']);
            var_dump($result);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    public function obtenerContactos(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;
        $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
        $nombreSesion = TelegramUtil::obtenerNombreSesion($cliente, $canalId);
        $madelineProto = new \danog\MadelineProto\API($nombreSesion, $settings);

        try {
            $contactsContacts = $madelineProto->contacts->getContacts();
            echo json_encode($contactsContacts);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }
}
