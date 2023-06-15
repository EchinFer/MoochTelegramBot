<?php

namespace App\Telegram;

use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Database\Mysql;
use danog\MadelineProto\Settings\Logger;
use Exception;

class TelegramUtil
{
    public static function formatearNombreSesion($cliente, $canalId) : string {
        $nombreSesion = "session.madeline.$cliente.$canalId";
        return $nombreSesion;
    }

    public static function obtenerApiInfo($cliente) {
        $appInfoClientes = [
            "telefuturo" => [
                "canal-1" => [
                    "api_id" => 20585216,
                    "api_hash" => "488684162b7185ba741f42afa417b034"
                ],
                "canal-2" => [
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
        if($apiInfo == null){
            throw new Exception("El cliente '$cliente' no tiene configurado la informacion de la app en Telegram");
            return null;
        }
        if(!isset($apiInfo[$canalId])){
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
        $databaseSetting = $databaseSetting->setUri(env("DB_HOST"));
        $databaseSetting = $databaseSetting->setDatabase($cliente);
        $databaseSetting = $databaseSetting->setUsername(env("DB_USERNAME"));
        $databaseSetting = $databaseSetting->setPassword(env("DB_PASSWORD"));


        $settings->setAppInfo($appinfoSetting);
        $settings->setConnection($connectionSetting);
        // $settings->setDb($databaseSetting);
        // $settings->setLogger($loggerSetting);
        

        return $settings;
    }
}
