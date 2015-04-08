<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 2:27 PM
 */

namespace Tops\sys;


interface IMailer {
    public function send(TEMailMessage $message);
    public function setSendEnabled($value);
}