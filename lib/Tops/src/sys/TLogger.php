<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/29/2015
 * Time: 7:57 AM
 */

namespace Tops\sys;
use Monolog\Logger;

/**
 * Class TLogger
 * @package Tops\sys
 */
class TLogger implements ILogger
{
    private $logManager;
    private $defaultLogName;
    public function __construct($defaultLogName = 'default', TLogManager $logManager = null) {
        if ($logManager !== null) {
            $this->logManager = $logManager;
        }
        $this->defaultLogName = $defaultLogName;
    }

    /**
     * Used for unit test only
     *
     * @return string
     */
    public function getDefaultLogName() {
        return $this->defaultLogName;
    }
    public function setDefaultLogName($value) {
        $this->defaultLogName = $value;
    }

    /**
     * @return mixed|TLogManager
     */
    private function getLogManager() {
        if (!isset($this->logManager)) {

            $this->logManager = TLogManager::GetInstance();
        }
        return $this->logManager;
    }


    /**
     * @param $name
     * @param Logger $logger
     * @param bool $isDefault
     */
    public function setLog($name, Logger $logger, $isDefault = false)
    {
        $this->getLogManager()->setLog($name, $logger);
        if ($isDefault) {
            $this->defaultLogName = $name;
        }
    }

    /**
     * @param $name
     * @return Logger
     */
    public function getLog($name)
    {
        $log = $this->getLogManager()->getLog($name);
        if ($log == null) {
            $log = $this->logManager->getLog($this->defaultLogName);
        }
        return $log;
    }

    /**
     * @inheritdoc
     */
    public function write($message, $level = Logger::ERROR, $logName = null) {
        if ($logName === null) {
            $logName = $this->defaultLogName;
        }
        $log = $this->getLog($logName);
        $log->addRecord($level,$message);
    }

    /**
     * @inheritdoc
     */
    public function writeError($message, $logName = null) {
        $this->write($message, Logger::ERROR, $logName);
    }

    /**
     * @inheritdoc
     */
    public function writeWarning($message, $logName = null) {
        $this->write($message, Logger::WARNING, $logName);

    }

    /**
     * @inheritdoc
     */
    public function writeInfo($message, $logName = null) {
        $this->write($message, Logger::INFO, $logName);
    }


}