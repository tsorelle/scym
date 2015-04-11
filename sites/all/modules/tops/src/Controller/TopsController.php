<?php
namespace Drupal\tops\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tops\services\TServiceHost;


class TopsController extends ControllerBase {

    public function executeService(Request $request) { // }, $serviceCode) {
        $result = TServiceHost::ExecuteRequest($request); // , $serviceCode);
        return new JsonResponse($result);
    }

    public function getFromService(Request $request, $serviceCode,$serviceRequest) {
        $result = TServiceHost::ExecuteRequest($request, $serviceCode, $serviceRequest);
        return new JsonResponse($result);

    }

    public function  test() {
        $account = $this->currentUser();
        $username = $account->getUsername();
        $result = new \stdClass();
        $result->data = $username;
        $response = new JsonResponse($result);
        return $response;
    }

    public function test2($serviceCode, $argument) {
        $result = new \stdClass();
        $result->serviceCode = $serviceCode;
        $result->argument = $argument;
        $response = new JsonResponse($result);
        return $response;

    }

}
