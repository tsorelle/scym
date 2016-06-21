<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/21/2016
 * Time: 8:14 AM
 */

namespace App\services;


use Tops\services\TServiceCommand;
use Tops\sys\TUser;

class InitRegisterDocumentCommand extends TServiceCommand
{

    protected function run()
    {
        if (!TUser::getCurrent()->isAuthorized('register documents')) {
            $this->addErrorMessage('User not authorized to register documents.');
            return;
        }
        $result = new \stdClass();
        $result->publicPath =  'public_html/'.variable_get('file_public_path', conf_path() . '/files');
        $result->privatePath = 'public_html/'.variable_get('file_private_path');
        $this->setReturnValue($result);
    }
}