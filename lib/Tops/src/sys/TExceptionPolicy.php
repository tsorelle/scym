<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/1/2015
 * Time: 7:18 AM
 */

namespace Tops\sys;


class TExceptionPolicy {

    public function __construct($name, $severity = TException::SeverityError, $rethrow = null, $logName = null ) {
        $this->name = $name;
        $this->severity  = $severity;
        if ($rethrow === null) {
            $rethrow = empty($logName);
        }
        $this->rethrow = $rethrow;
        $this->logName = $logName;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean | null
     */
    public function getRethrow()
    {
        return $this->rethrow;
    }

    /**
     * @param boolean $rethrow
     */
    public function setRethrow($rethrow)
    {
        $this->rethrow = $rethrow;
    }

    /**
     * @return string
     */
    public function getLogName()
    {
        return $this->logName;
    }

    /**
     * @param string $logName
     */
    public function setLogName($logName)
    {
        $this->logName = $logName;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param $severity
     * @internal param int $Severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @var string
     */
    private $name;
    /**
     * @var boolean
     */
    private $rethrow;
    /**
     * @var string
     */
    private $logName;
    /**
     * @var int
     */
    private $severity;
}