<?php

namespace App\Telegram;

use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Database\Mysql;
use danog\MadelineProto\Settings\Logger;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TelegramUtil
{

    public static function obtenerNombreSesion($cliente, $canalId, $fullPath = null): string
    {
        $fullPath_ = self::obtenerRutaSesion($cliente, $fullPath);
        $nombreSesion = $fullPath_."/session.madeline.$cliente.$canalId";
        return $nombreSesion;
    }

    public static function obtenerApiInfo($cliente)
    {
        $appInfoClientes = [
            "telefuturo" => [
                "canal_1" => [
                    "api_id" => 20585216,
                    "api_hash" => "488684162b7185ba741f42afa417b034"
                ],
                "canal_2" => [
                    "api_id" => 20585216,
                    "api_hash" => "488684162b7185ba741f42afa417b034"
                ]
            ]
        ];

        $appInfo =  isset($appInfoClientes[$cliente]) ? $appInfoClientes[$cliente] : null;
        return $appInfo;
    }

    public static function obtenerConfiguracionInicial($cliente, $canalId)
    {

        $settings = new Settings();
        $apiInfo = self::obtenerApiInfo($cliente);
        if ($apiInfo == null) {
            throw new Exception("El cliente '$cliente' no tiene configurado la informacion de la app en Telegram");
            return null;
        }
        if (!isset($apiInfo[$canalId])) {
            throw new Exception("El canal '$canalId' no tiene configurado la informacion de la app en Telegram");
            return null;
        }

        $apiId = (int)$apiInfo[$canalId]["api_id"];
        $apiHash = (string)$apiInfo[$canalId]["api_hash"];

        $appinfoSetting = new AppInfo;
        $appinfoSetting = $appinfoSetting->setApiId($apiId);
        $appinfoSetting = $appinfoSetting->setApiHash($apiHash);

        $loggerSetting = new Logger;
        $loggerSetting = $loggerSetting->setExtra("logMadeline/madeline.log");

        $connectionSetting = new Connection;
        $connectionSetting = $connectionSetting->setTestMode(true);

        $databaseSetting = new Mysql;
        // $databaseSetting = $databaseSetting->setUri(env("DB_HOST"));
        // $databaseSetting = $databaseSetting->setDatabase($cliente);
        // $databaseSetting = $databaseSetting->setUsername(env("DB_USERNAME"));
        // $databaseSetting = $databaseSetting->setPassword(env("DB_PASSWORD"));


        $settings->setAppInfo($appinfoSetting);
        // $settings->setConnection($connectionSetting);
        // $settings->setDb($databaseSetting);
        // $settings->setLogger($loggerSetting);


        return $settings;
    }


    public static function obtenerRutaSesion($cliente, $fullPath = null){
        $fullPath_ = $fullPath == null ? storage_path('framework/telegramSessions') : $fullPath;
        if(file_exists($fullPath_) == false){
            mkdir($fullPath_, 0777);
        }
        $fullPath_ = $fullPath == null ? storage_path('framework/telegramSessions/'.$cliente) : $fullPath."/".$cliente;
        if(file_exists($fullPath_) == false){
            mkdir($fullPath_, 0777);
        }
        
        return $fullPath_;
    }

}
