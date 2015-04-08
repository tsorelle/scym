<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/25/14
 * Time: 3:13 PM
 */

namespace Tops\services;
use Tops\sys\IUser;
use Tops\sys\TSession;
use Tops\sys\TUser;

/**
 * Class TServiceCommand
 * @package Tops\services
 */
abstract class TServiceCommand {
    /**
     * @var TServiceContext
     */
    private  $context;
    
    private  $request;

    /**
     * @var array
     */
    private $authorizations = array();

    abstract protected function run();

    protected function addErrorMessage($text) {
        $this->context->AddErrorMessage($text);
    }

    public function addInfoMessage($text) {
        $this->context->AddInfoMessage($text);
    }

    public function addWarningMessage($text) {
        $this->context->AddWarningMessage($text);
    }

    public function setReturnValue($value) {
        $this->context->SetReturnValue($value);
    }

    public function getRequest() {

        return $this->request;
    }

    /**
     * @var IUser
     */
    private $user;

    /**
     * @return IUser
     */
    protected function getUser() {
        if (!isset($this->user)) {
            $this->user = TUser::getCurrent();
        }
        return $this->user;
    }


    public function isAuthorized() {
        if (empty($this->authorizations))
            return true;
        $user = $this->getUser();
        foreach($this->authorizations as $auth) {
            if ($user->isAuthorized($auth)) {
                return true;
            }
        }
        return false;
    }

    protected function addAuthorization($authorization) {
        if (!in_array($authorization, $this->authorizations)) {
            array_push($this->authorizations, $authorization);
        }
    }


    /**
     * @param $request
     * @return TServiceResponse
     */
    public function execute($request, $securityToken = null) {
        $this->context = new TServiceContext();
        if (TSession::AuthenitcateSecurityToken($securityToken)) {
            $this->request = $request;
            if ($this->isAuthorized())
                $this->run();
            else
                $this->addErrorMessage("Sorry, you are not authorized to use this service.");
        }
        else {
            $this->addErrorMessage("Sorry, your session has expired or is not valid. Please return to home page.");
        }
        return $this->context->GetResponse();
    }
}
