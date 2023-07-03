<?php 



// declare(strict_types=1);
namespace App\Telegram;

// If a stable version of MadelineProto was installed via composer, load composer autoloader
if (file_exists('../../vendor/autoload.php')) {
    require_once '../../vendor/autoload.php';
}

use App\Http\Controllers\Api\Telegram\TelegramController;
use danog\MadelineProto\API;
use danog\MadelineProto\Db\DbArray;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Logger as SettingsLogger;
use danog\MadelineProto\Settings\Templates;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class TelegramEventHandler extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    public $admin = "6084274322"; // !!! Change this to your username !!!
    /**
     * Use this *only* if the data you will store here is huge (>100MB).
     * @var DbArray<array-key, array>
     */
    protected DbArray $dataStoredOnDb;

    /**
     * Otherwise use this.
     * This property is also saved in the db, but it's also always kept in memory, unlike $dataStoredInDb which is exclusively stored in the db.
     */
    protected array $dataAlsoStoredOnDbAndInRam = [];

    /**
     * This property is also saved in the db, but it's also always kept in memory, unlike $dataStoredInDb which is exclusively stored in the db.
     * @var array<int, bool>
     */
    protected array $notifiedChats = [];

    /**
     * This property is also saved in the db, but it's also always kept in memory, unlike $dataStoredInDb which is exclusively stored in the db.
     */
    private int $adminId;

    public function __construct($admin = null) {
        $this->admin = $admin; // !!! Change this to your username !!!
    }

    /**
     * Get peer(s) where to report errors.
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return [$this->admin];
    }
    /**
     * Initialization logic.
     */
    public function onStart(): void
    {
        $this->logger("The handler was started!");
        $this->logger(["log-getInfo" => $this->getInfo($this->admin)]);
        $this->logger(["log-getId" => $this->getId($this->admin)]);
        // $this->logger($this->getId($this->admin));
        $this->adminId = $this->getId($this->admin);

        // if ($this->getSelf()['bot'] && $this->getSelf()['id'] === $this->adminId) {
        //     return;
        // }
        // $this->messages->sendMessage(
        //     peer: $this->admin,
        //     message: "The bot was started!"
        // );
    }
    /**
     * Handle updates from supergroups and channels.
     *
     * @param array $update Update
     */
    public function onUpdateNewChannelMessage(array $update): void
    {
        $this->onUpdateNewMessage($update);
    }

    /**
     * Handle updates from users.
     *
     * 100+ other types of onUpdate... method types are available, see https://docs.madelineproto.xyz/API_docs/types/Update.html for the full list.
     * You can also use onAny to catch all update types (only for debugging)
     * A special onUpdateCustomEvent method can also be defined, to send messages to the event handler from an API instance, using the sendCustomEvent method.
     *
     * @param array $update Update
     */
    public function onUpdateNewMessage(array $update): void
    {
        if ($update['message']['_'] === 'messageEmpty') {
            return;
        }
        $this->logger(["log-onUpdateNewMessage" => $update]);

        // Chat ID
        $id = $this->getId($update);
        $this->logger(["log-onUpdateNewMessage-chat_id" => $id]);

        // Sender ID, not always present
        $from_id = isset($update['message']['from_id'])
            ? $this->getId($update['message']['from_id'])
            : null;

        $message = $update['message']['message'] ?? '';
        
        if($from_id === $this->adminId){
            if ($message === 'restart-event-handler') {
                $this->messages->sendMessage(['message' => 'Reiniciar Event Handler', 'peer' => $update]);
                $this->restart();
            }

            if ($message === 'get-chat-id') {
                $chatFullInfo = $this->getInfo($id);
                $this->logger(["log-chatFullInfo" => $chatFullInfo]);
            }
        }
        
        if ($message === 'ping') {
            $this->messages->sendMessage(['message' => 'pong', 'peer' => $update]);
        }

        if ($message === 'restart-event-handler') {
            if($from_id === $this->adminId){
                $this->messages->sendMessage(['message' => 'Reiniciar Event Handler', 'peer' => $update]);
                $this->restart();
            }
        }

        if ($message === 'stop') {
            if($from_id === $this->adminId){
                $this->stop();
            }
           
        }

        if ($message === 'saludame') {
            
            if($from_id != null && $from_id != $this->adminId){
                $info = $this->getFullInfo($from_id);
                if($info["type"] == "user"){
                    $this->logger(["log-info-saludame" => $info]);
                    $userInfo = $info["User"];
                    $this->logger(["log-info-user-saludame" => $userInfo]);
                    $anyUserName = "";
                    if(isset($userInfo["first_name"]) && isset($userInfo["last_name"])){
                        $anyUserName = $userInfo["first_name"]. " ".$userInfo["last_name"]." ";
                    }
                    
                    $mensajeSaludo = "Holaa! ".$anyUserName. "¿Con mucho frio?";
                    $this->messages->sendMessage(['message' => $mensajeSaludo, 'peer' => $update]);
                }
            }
        }
    }

    public function onAny($update): void
    {
        $this->logger(["log-onAny" => $update]);
    }
}

// $MadelineProto->phoneLogin($MadelineProto->readline('Enter your phone number: '));
// $authorization = $MadelineProto->completePhoneLogin($MadelineProto->readline('Enter the phone code: '));
// if ($authorization['_'] === 'account.password') {
//     $authorization = $MadelineProto->complete2falogin($MadelineProto->readline('Please enter your password (hint '.$authorization['hint'].'): '));
// }
// if ($authorization['_'] === 'account.needSignup') {
//     $authorization = $MadelineProto->completeSignup($MadelineProto->readline('Please enter your first name: '), readline('Please enter your last name (can be empty): '));
// }

// return;


$fullPathSessions = __DIR__."/../../storage/framework/telegramSessions";
$telegramController = new TelegramController();

$telegramController->iniciarSesion("telefuturo", "canal_1", $fullPathSessions);

// echo $telegramController->iniciarManejadorEventosMtpro($fullPathSessions);
