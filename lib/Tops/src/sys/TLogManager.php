<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/7/2015
 * Time: 9:20 AM
 */

namespace Tops\sys;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class TLogManager {
    /**
     * @var array
     */
    private $logs;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var IMailer
     */
    private $mailer;

    /**
     * @var TLogManager
     */
    private static $instance;

    private static $testlog;
    public static function GetTestLog() {
        return self::$testlog;
    }

    public static function CreateInstance() {
        self::$instance = new TLogManager();
        return self::$instance;
    }

    public static function GetInstance()
    {
        if (!isset(self::$instance)) {
            if (TObjectContainer::HasDefinition("logManager")) {
                self::$instance = TObjectContainer::Get('logManager');
            }
            else {
                self::$instance = new TLogManager();
            }
        }
        return self::$instance;
    }




    /**
     * @param IConfigManager $configManager
     * @param IMailer $mailer
     */
    public function __construct(IConfigManager $configManager = null, IMailer $mailer = null) {
        $this->logs = array();
        $this->configs = array();
        if ($configManager != null) {
            $config = $configManager->getLocal("appsettings","logging");
            $loggingConfig = $config->Value("logs");
            if (!empty($loggingConfig)) {
                foreach ($loggingConfig as $section) {
                    $logConfig = new TConfigSection($section);
                    $logName = $logConfig->Value("name",'default');
                    $this->configs[$logName] = $logConfig;
                }
            }
        }
    }

    public function getLog($logName)
    {
        if (!array_key_exists($logName,$this->logs)) {
            if (!array_key_exists($logName,$this->configs)) {
                $logName = 'default';
                if (!array_key_exists($this->configs, 'default')) {
                    return null;
                }
            }
            $this->logs[$logName] = $this->createLogFromConfig($logName, $this->configs[$logName]);
        }

        return $this->logs[$logName];
    }

    public function setLog($logName, Logger $log)
    {
        $this->logs[$logName] = $log;
    }

    /**
     * @param IConfiguration $handlerConfig
     * @return \Monolog\Handler\HandlerInterface
     * @internal param IMailer $mailer
     */
    protected function createHandler(IConfiguration $handlerConfig)
    {
        $level = $this->levelNameToLevel($handlerConfig->Value('level','error'));
        $type = $handlerConfig->Value("type");
        $recipient = $handlerConfig->Value('recipient');
        $sender = $handlerConfig->Value('sender');
        if ($type == 'email' && ($this->mailer == null || empty($recipient) || empty($sender))) {
            //insufficient configuration for email.
            $type = 'default';
        }

        $bubble = $handlerConfig->IsTrue("bubble",false);
        $expandNewlines = $handlerConfig->IsTrue('expandlines',true);

        $filePath = $handlerConfig->Value('filepath');
        if (!empty($filePath)&& $filePath[0] == '~') {
            $filePath = TPath::FromRoot($filePath);
        }
        $maxFiles = $handlerConfig->Value('maxfiles');
        $permissions = $handlerConfig->Value('filepermissions');
        $uselocking = $handlerConfig->IsTrue('filelocking');

        switch ($type) {
            case 'errorlog' :
                return new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM,$level,$bubble,$expandNewlines);
            case 'email' :
                $handler = new TMailLogHandler($this->mailer, $sender, $recipient, $level,$bubble);
                return $handler;
            case 'testlog' :
                self::$testlog = new TestHandler($level,$bubble);
                return self::$testlog;
            case 'chromelog' :
                return new ChromePHPHandler($level,$bubble);
            case 'streamlog' :
                return new StreamHandler($filePath,$level,$bubble,$permissions,$uselocking);
            case 'rotatinglog' :
                return new RotatingFileHandler($filePath, empty($maxFiles) ? 0 : $maxFiles, $level,$bubble,
                    $permissions,empty($uselocking));
            default :
                return new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM,$level,$bubble,$expandNewlines);
        }
    }

    /**
     * @param $levelName
     * @return int
     */
    protected function levelNameToLevel($levelName) {
        switch ($levelName) {
            // 100 => 'DEBUG',
            case 'debug' :
                return Logger::DEBUG;
            // 200 => 'INFO',
            case 'info':
                return Logger::INFO;
            // 250 => 'NOTICE',
            case 'notice' :
                return Logger::NOTICE;
            // 300 => 'WARNING',
            case 'warning' :
                return Logger::WARNING;
            // 400 => 'ERROR',
            case 'error' :
                return Logger::ERROR;
            // 500 => 'CRITICAL',
            case 'critical' :
                return Logger::CRITICAL;
            // 550 => 'ALERT',
            case 'alert' :
                return Logger::ALERT;
            // 600 => 'EMERGENCY',
            case 'emergency' :
                return Logger::EMERGENCY;
            default:
                return LOGGER::DEBUG;
        }
    }

    /**
     * @param $logName
     * @param TConfigSection $logConfig
     * @return Logger
     * @internal param IMailer $mailer
     */
    protected function createLogFromConfig($logName, TConfigSection $logConfig)
    {
        $handlers = $logConfig->Value("handlers", array());
        $log = new Logger($logName);

        foreach ($handlers as $handlerElement) {
            $handlerConfig = new TConfigSection($handlerElement);
            $handler = $this->createHandler($handlerConfig);
            $log->pushHandler($handler);
        }
        return $log;
    }

}