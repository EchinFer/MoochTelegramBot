<?php 



// declare(strict_types=1);
namespace App\Telegram;

// If a stable version of MadelineProto was installed via composer, load composer autoloader
if (file_exists('../../vendor/autoload.php')) {
    require_once '../../vendor/autoload.php';
}

use danog\MadelineProto\API;
use danog\MadelineProto\Db\DbArray;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Logger as SettingsLogger;
use danog\MadelineProto\Settings\Templates;
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
}

$settings = new Settings();
        
$appinfo = new AppInfo;
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

$loggerSetting = new SettingsLogger;
$loggerSetting = $loggerSetting->setExtra("logMadeline/madeline.log");

$connectionSetting = new Connection;
$connectionSetting = $connectionSetting->setTestMode(true);

$settings->setAppInfo($appinfo);
$settings->setTemplates($templateSetting);
// $settings->setLogger($loggerSetting);

$madeline = new API("session.madeline.user2", $settings);
TelegramEventHandler::startAndLoop('session.madeline.user2', $settings);