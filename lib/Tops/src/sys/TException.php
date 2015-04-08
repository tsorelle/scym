<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/2/2015
 * Time: 6:23 AM
 */

namespace Tops\sys;


class TException extends \Exception implements IException {
    const SeverityUnknown = 0;
    const SeverityMinor = 300;
    const SeverityError = 400;
    const SeverityCritical=500;
    const SeverityAlert=550;
    const SeverityEmergency=600;

    private $severity;

    public function __construct($message = "", $severity = TException::SeverityError, \Exception $previous = null) {
        $this->severity = $severity;
        parent::__construct($message,0,$previous);
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

}