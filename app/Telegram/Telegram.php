<?php


namespace App\Telegram;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use PhpTelegramBot\FluentKeyboard\InlineKeyboard\InlineKeyboardButton;
use PhpTelegramBot\FluentKeyboard\InlineKeyboard\InlineKeyboardMarkup;
use PhpTelegramBot\FluentKeyboard\ReplyKeyboard\KeyboardButton;
use PhpTelegramBot\FluentKeyboard\ReplyKeyboard\ReplyKeyboardMarkup;

class Telegram
{

    private static function addOptionsToReplyKeyboard(ReplyKeyboardMarkup $replyKeyboard, array $keyboardOptions) : ReplyKeyboardMarkup{

        $isPersistent = isset($keyboardOptions["isPersistent"]) ? $keyboardOptions["isPersistent"] : false;
        $oneTimeKeyboard = isset($keyboardOptions["oneTimeKeyboard"]) ? $keyboardOptions["oneTimeKeyboard"] : false;
        $resizeKeyboard = isset($keyboardOptions["resizeKeyboard"]) ? $keyboardOptions["resizeKeyboard"] : false;

        if($oneTimeKeyboard){
            $replyKeyboard = $replyKeyboard->oneTimeKeyboard();
        }
        if($isPersistent){
            $replyKeyboard = $replyKeyboard->isPersistent();
        }
        if($resizeKeyboard){
            $replyKeyboard = $replyKeyboard->resizeKeyboard();
        }

        return $replyKeyboard;
    }

    public static function makeReplayKeyboardButtons(array $buttons, array $keyboardOptions = null) : ReplyKeyboardMarkup
    {
        $buttonsPerRow = isset($keyboardOptions["buttonsPerRow"]) ? $keyboardOptions["buttonsPerRow"] : 3;

        $countButtons = count($buttons);
        $rowCount = ceil($countButtons / $buttonsPerRow);

        $replyKeyboard = ReplyKeyboardMarkup::make();

        if($keyboardOptions != null){
            $replyKeyboard = self::addOptionsToReplyKeyboard($replyKeyboard, $keyboardOptions);
        }

        $offset = 0;    
        $length = $buttonsPerRow;

        //ARMAR LOS BOTONES PARA EL TECLADO DE RESPUESTA
        $replyKeyboardButtons = [];
        foreach ($buttons as $value) {
            $replyKeyboardButtons[] = KeyboardButton::make($value["label"])->callbackData($value["replyData"]);
        }

        //LIMITAR LA CANTIDAD DE BOTONES QUE HAY EN CADA FILA
        for ($i=0; $i < $rowCount; $i++) {
            $rowButtons = array_slice(
                $replyKeyboardButtons,
                $offset,
                $length
            );
            $replyKeyboard = $replyKeyboard->row($rowButtons);
            $offset = $offset + $length;
        }

        return $replyKeyboard;
    }

    public static function makeInlineKeyboardButtons(array $buttons, int $buttonsPerRow = 3) : InlineKeyboardMarkup
    {
        $countButtons = count($buttons);

        $rowCount = ceil($countButtons / $buttonsPerRow);

        $inlineKeyboard = InlineKeyboardMarkup::make();
        $offset = 0;
        $length = $buttonsPerRow;

        //ARMAR LOS BOTONES PARA EL TECLADO EN LINEA
        $inlineKeyboardButtons = [];
        foreach ($buttons as $value) {
            $inlineKeyboardButtons[] = InlineKeyboardButton::make($value["label"])->callbackData($value["replyData"]);
        }

        //LIMITAR LA CANTIDAD DE BOTONES QUE HAY EN CADA FILA
        for ($i=0; $i < $rowCount; $i++) {
            $rowButtons = array_slice(
                $inlineKeyboardButtons,
                $offset,
                $length
            );
            $inlineKeyboard = $inlineKeyboard->row($rowButtons);
            $offset = $offset + $length;
        }

        return $inlineKeyboard;
    }

    public static function replyKeyboardRemove(string $chatId, string $text) : ServerResponse
    {

        $result = Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
        ]);

        return $result;
    }

    public static function sendTextMessage(string $chatId, string $text) : ServerResponse
    {
        
        $result = Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
        ]);

        return $result;
    }

    public static function sendPhotoMessage(string $chatId, string $imageUrl) : ServerResponse
    {
        $result = Request::sendPhoto([
            'chat_id' => $chatId,
            'photo'   => Request::encodeFile($imageUrl),
        ]);

        return $result;
    }

    public static function sendReplyKeyboardMessage(string $chatId, string $text, array $buttons, array $keyboardOptions = null ) : ServerResponse
    {   
        $replyKeyboard = self::makeReplayKeyboardButtons($buttons, $keyboardOptions);

        $response = Request::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $text,
            'reply_markup' => $replyKeyboard
        ]);
        return $response;
    }

    public static function sendInlineKeyboardMessage(string $chatId, string $text, array $buttons, int $buttonsPerRow = 3) : ServerResponse
    {
        $inlineKeyboard = self::makeInlineKeyboardButtons($buttons, $buttonsPerRow);
        $response = Request::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $text,
            'reply_markup' => $inlineKeyboard
        ]);
        return $response;
    }

}

