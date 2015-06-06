<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/28/14
 * Time: 6:42 AM
 */
namespace Tops\services;
use MyProject\Proxies\__CG__\stdClass;
use Symfony\Component\Config\Definition\Exception\Exception;
use \Symfony\Component\HttpFoundation\Request;
use Tops\sys\IConfiguration;
use Tops\sys\IConfigManager;
use Tops\sys\IExceptionHandler;
use Tops\sys\IUserFactory;
use Tops\sys\TObjectContainer;
use Tops\sys\IUser;
use Tops\sys\TUser;

/**
 * Class TServiceHost
 * @package Tops\services
 */
class TServiceHost {

    /**
     * @var TServiceHost
     */
    private static $instance;
    private static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = TObjectContainer::Get('serviceHost');
        }
        return self::$instance;
    }

    /**
     * @var IServiceFactory
     */
    private $serviceFactory;

    /**
     * @var IExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var TServiceResponse
     */
    private $failureResponse;


    public function __construct(IServiceFactory $serviceFactory, IUserFactory $userFactory = null, IExceptionHandler $exceptionHandler = null) {
        $this->serviceFactory = $serviceFactory;
        $this->exceptionHandler = $exceptionHandler;

        if ($userFactory !== null) {
            TUser::setUserFactory($userFactory);
        }
    }

    /**
     * @return TServiceResponse
     */
    private function getFailureResponse($debugInfo = null) {
        if (!isset($this->failureResponse)) {
            $this->failureResponse = new TServiceErrorResponse();
            $this->failureResponse->Result = ResultType::ServiceFailure;
            $message = new TServiceMessage();
            $message->MessageType = MessageType::Error;
            $message->Text = 'Service failed. If the problem persists contact the site administrator.';
            $this->failureResponse->Messages = array($message);
            $this->failureResponse->debugInfo = $debugInfo;
        }
        return $this->failureResponse;
    }

    private function handleException($ex) {
        // throw $ex;
        if (empty($this->exceptionHandler)) {
            return true;
        }
        return $this->exceptionHandler->handleException($ex);
    }

    /**
     * @param Request $request
     * @return TServiceResponse
     * @throws \Exception
     */
    public static function ExecuteRequest(Request $request = null, $serviceCode = null, $serviceRequest = null)
    {
        $instance = self::getInstance();
        try {
            return $instance->_executeRequest($request, $serviceCode, $serviceRequest);
        }
        catch (\Exception $ex) {
            $rethrow = $instance->handleException($ex);
            if ($rethrow) {
                throw $ex;
            }

            $debugInfo = new \stdClass();
            $debugInfo->message = $ex->getMessage();
            $debugInfo->location = $ex->getFile().": Line ".$ex->getLine();
            $debugInfo->trace = $ex->getTraceAsString();

            return $instance->getFailureResponse($debugInfo);
        }

    }

    /**
     * @var IConfiguration
     */
    private static $tokensEnabled;

    /**
     * @return IConfiguration
     */
    private static function tokensAreEnabled() {
        if (!isset(self::$tokensEnabled)) {
            /** @var IConfigManager $configManager */
            $configManager = TObjectContainer::Get('configManager');
            $securityConfig = $configManager->getLocal('appsettings','security');
            self::$tokensEnabled = $securityConfig->IsTrue('xsstokens',true);
        }
        return self::$tokensEnabled;
    }



    private function getSecurityToken(Request $request) {
        if ($request != null && self::tokensAreEnabled()) {
            $securityToken = $request->get('topsSecurityToken');
            if (!$securityToken) {
                return 'invalid';
            }
        }
        return '';
    }

    private function _executeRequest(Request $request = null, $serviceCode = null, $serviceRequest = null)
    {

        // try {
        if (empty($request)) {
            $request = Request::createFromGlobals();
        }


        if ($serviceCode == null) {
            $serviceCode = $request->get('serviceCode');
        }
        if (empty($serviceCode)) {
            throw new \Exception('No service command id was in request');
        }

        $serviceCode = str_replace('.', '\\', $serviceCode);

        $input = null;
        if ($serviceRequest == null) {
            $serviceRequest = $request->get('request');
        }

        $requestMethod = $request->getMethod();
        if ($requestMethod == 'POST') {
            if ($serviceRequest != null) {
                $input = json_decode($serviceRequest);
            }
        } else {
            if ($requestMethod == 'GET') {
                if ($serviceRequest != null) {
                    $input = $serviceRequest;
                }
            } else {
                throw new \Exception('Unsupported request method: ' . $request->getMethod());
            }
        }
        $securityToken = $this->getSecurityToken($request);
        return $this->_execute($serviceCode, $input, $securityToken);
    }

    /**
     * @param $serviceCode
     * @param $input
     * @param null $securityToken
     * @return TServiceResponse
     * @throws \Exception
     */
    public static function Execute($serviceCode, $input, $securityToken = null) {
        return self::getInstance()->_execute($serviceCode, $input, $securityToken);
    }

    public function _execute($serviceCode, $input, $securityToken = null) {
        $command = $this->serviceFactory->CreateService($serviceCode);
        if (empty($command))
            throw new \Exception("Unable to create service command '$serviceCode'");
        return $command->Execute($input, $securityToken);
    }
} 