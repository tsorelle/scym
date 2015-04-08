<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/2/2015
 * Time: 6:51 AM
 */

namespace Tops\sys;


class TErrorException extends \ErrorException implements IException {
    public static function SetErrorHandler($functionName = '\Tops\sys\TErrorException::errorHandler', $errorTypes = E_ALL) {
        set_error_handler($functionName,$errorTypes);
    }

    public static function GetErrorSeverity($errorCode=0) {
        switch($errorCode) {
            case E_COMPILE_ERROR:
            case E_PARSE:
                return TException::SeverityCritical;
            case E_CORE_ERROR:
                return TException::SeverityAlert;
        }
        return TException::SeverityError;
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline ) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return false;
        }
        $severity = self::GetErrorSeverity($errno);
        throw new TErrorException($errstr,$errno,$severity, $errfile, $errline);
    }

}

