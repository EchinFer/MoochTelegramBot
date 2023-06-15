<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramEventHandler;
use App\Telegram\TelegramUtil;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Logger;
use danog\MadelineProto\Settings\Templates;
use danog\MadelineProto\Settings\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Revolt\EventLoop;

class MadelineProtoController extends Controller
{
    public $settings;
    public $madelineProto;

    public function __construct()
    {
        // $settings = new Settings();

        // $appinfo = (new AppInfo);
        // $appinfo = $appinfo->setApiId('20585216');
        // $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        // $templateSetting = (new Templates)
        // ->setHtmlTemplate('
        //     <!DOCTYPE html>
        //     <html>
        //             <head>
        //                 <title>MadelineProto Test</title>
        //             </head>
        //             <body>
        //                 <h1>MadelineProto Test</h1>
        //                 <form method="POST" action="/madeline_proto/home">
        //                     %s
        //                     <button type="submit"/>Comenzar</button>
        //                     </form>
        //                     <p>%s</p>
        //             </body>
        //     </html>
        // ');        
        // $settings->setAppInfo($appinfo);
        // $settings->setTemplates($templateSetting);

        // $this->settings = $settings;

        // $this->madelineProto = new \danog\MadelineProto\API('session.madeline', $this->settings); // The session will be serialized to session.madeline
        // $this->madelineProto->start();
    }

    public function startHandlerLoop(Request $request)
    {
        $cliente = $request->cliente;
        $canalId = $request->canalId;

        try {
            $settings = TelegramUtil::obtenerConfiguracionInicial($cliente, $canalId);
            $nombreSesion = TelegramUtil::formatearNombreSesion($cliente, $canalId);

            \Revolt\EventLoop::queue(TelegramEventHandler::startAndLoop(...), $nombreSesion, $settings);
        } catch (\Throwable $th) {
            echo $th->getMessage();
            return false;
        }

        return true;
    }

    public function startLogin()
    {

        $settings = new Settings();

        $appinfo = (new AppInfo)
            ->setApiId('20585216');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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
        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2', $settings); // The session will be serialized to session.madeline

        return $madelineProto->start();
    }

    public function home()
    {
        $settings = new Settings();

        $appinfo = (new AppInfo)
            ->setApiId('20585216');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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

        var_dump($appinfo->getApiId());
        return;


        try {
            $madelineProto = new \danog\MadelineProto\API('session.madeline', $settings); // The session will be serialized to session.madeline
            // $madelineProto->async(true); /* adding this string */
            $madelineProto->botLogin('6025075626:AAEMfLD54AnvBQ52vELtp-YMbLci8i_f9EA');
            // $madelineProto->start();
            $madelineProto->messages->sendMessage(['peer' => '6084274322', 'message' => 'Hola Mundo']);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function loginBot()
    {
        $settings = new Settings();

        $appinfo = (new AppInfo);
        $appinfo = $appinfo->setApiId('20585216');
        $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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
        // $madelineProto->start();

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

    public function passwordPhoneLogin(Request $request){
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

    public function sendMessageFromUser(Request $request)
    {
        $settings = new Settings();

        $appinfo = (new AppInfo);
        $appinfo = $appinfo->setApiId('20585216');
        $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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

        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2', $settings); // The session will be serialized to session.madeline.example
        // $madelineProto->start();

        try {
            // return $madelineProto->start();
            $result = $madelineProto->messages->sendMessage(['peer' => '595971655455', 'message' => 'Hola soy un bot jajadjad']);

            var_dump($result);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    public function setWebhook()
    {
        $settings = new Settings();

        $appinfo = (new AppInfo);
        $appinfo = $appinfo->setApiId('20585216');
        $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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

        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2', $settings); // The session will be serialized to session.madeline.example
        // $madelineProto->start();

        try {
            // return $madelineProto->start();
            // $result = $madelineProto->messages->sendMessage(['peer' => '595971655455', 'message' => 'Hola soy un bot jajadjad']);
            $result = $madelineProto->setWebhook('https://developers2.newage.systems/laravel-telegram-bot/public/api/webhook-user');

            var_dump($result);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    public function getContacts()
    {
        $settings = TelegramUtil::obtenerConfiguracionInicial("telefuturo", "canal-1");

        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2', $settings); // The session will be serialized to session.madeline.example
        // $madelineProto->start();

        try {
            // return $madelineProto->start();
            // $result = $madelineProto->messages->sendMessage(['peer' => '595971655455', 'message' => 'Hola soy un bot jajadjad']);
            // $result = $madelineProto->setWebhook('https://developers2.newage.systems/laravel-telegram-bot/public/api/webhook-user');
            $contacts_Contacts = $madelineProto->contacts->getContacts();

            echo json_encode($contacts_Contacts);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    public function getId()
    {

        $settings = new Settings();

        $appinfo = (new AppInfo);
        $appinfo = $appinfo->setApiId('20585216');
        $appinfo = $appinfo->setApiHash('488684162b7185ba741f42afa417b034');

        $templateSetting = (new Templates)
            ->setHtmlTemplate('
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

        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2', $settings); // The session will be serialized to session.madeline.example
        // $madelineProto->start();

        try {
            // return $madelineProto->start();
            // $result = $madelineProto->messages->sendMessage(['peer' => '595971655455', 'message' => 'Hola soy un bot jajadjad']);
            // $result = $madelineProto->setWebhook('https://developers2.newage.systems/laravel-telegram-bot/public/api/webhook-user');
            // $contacts_Contacts = $madelineProto->contacts->getContacts();
            $dataId = $madelineProto->getId("@Moochfer");
            // $this->getId(self::ADMIN);
            echo json_encode($dataId);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    public function unsetEventHandler()
    {
        $madelineProto = new \danog\MadelineProto\API('session.madeline.user2');
        $madelineProto->unsetEventHandler();
    }
}
