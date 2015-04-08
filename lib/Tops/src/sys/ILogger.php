<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/29/2015
 * Time: 11:13 AM
 */
namespace Tops\sys;

use Monolog\Logger;

/**
 * Interface ILogger
 * @package Tops\sys
 */
interface ILogger
{
    /**
     * @param $message
     * @param int $level
     * @param string $logName
     * @return Boolean Whether the record has been processed
     */
    public function write($message, $level = Logger::ERROR, $logName = 'default');

    /**
     * @param $message
     * @param string $logName
     * @return Boolean Whether the record has been processed
     */
    public function writeError($message, $logName = null);

    /**
     * @param $message
     * @param string $logName
     * @return Boolean Whether the record has been processed
     */
    public function writeWarning($message, $logName = null);

    /**
     * @param $message
     * @param string $logName
     * @return Boolean Whether the record has been processed
     */
    public function writeInfo($message, $logName = null);

    public function setDefaultLogName($value);
}