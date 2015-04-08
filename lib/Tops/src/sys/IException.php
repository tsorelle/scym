<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/2/2015
 * Time: 6:29 AM
 */

namespace Tops\sys;


interface IException {
    // for TOPS custom exceptions
    /**
     * @return int
     */
    public function getSeverity();


    // from PHP \Exception

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the Exception message
     * @link http://php.net/manual/en/exception.getmessage.php
     * @return string the Exception message as a string.
     */
    public function getMessage();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the Exception code
     * @link http://php.net/manual/en/exception.getcode.php
     * @return mixed|int the exception code as integer in
     * <b>Exception</b> but possibly as other type in
     * <b>Exception</b> descendants (for example as
     * string in <b>PDOException</b>).
     */
    public function getCode();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the file in which the exception occurred
     * @link http://php.net/manual/en/exception.getfile.php
     * @return string the filename in which the exception was created.
     */
    public function getFile();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the line in which the exception occurred
     * @link http://php.net/manual/en/exception.getline.php
     * @return int the line number where the exception was created.
     */
    public function getLine();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the stack trace
     * @link http://php.net/manual/en/exception.gettrace.php
     * @return array the Exception stack trace as an array.
     */
    public function getTrace();

    /**
     * (PHP 5 &gt;= 5.3.0)<br/>
     * Returns previous Exception
     * @link http://php.net/manual/en/exception.getprevious.php
     * @return \Exception the previous <b>Exception</b> if available
     * or null otherwise.
     */
    public function getPrevious();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Gets the stack trace as a string
     * @link http://php.net/manual/en/exception.gettraceasstring.php
     * @return string the Exception stack trace as a string.
     */
    public function getTraceAsString();


}