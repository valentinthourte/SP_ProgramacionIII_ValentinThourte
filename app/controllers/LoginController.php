<?php

require_once("controllers/AController.php");
require_once("services/LoginService.php");

class LoginController extends AController {
    private $loginService;

    public function __construct() {
        $this->loginService = new LoginService();
    }
    public function loginUsuario($request, $response, $args) {
        try {
            $parametros = json_decode($request->getBody()->getContents(),true);
            $respuesta = $this->loginService->loguearUsuario($parametros);
            $content = json_encode($respuesta);
            return $this->setearResponse($response, $content);
        }
        catch (Exception $e) {
            return $this->setearResponseError($response, $e->getMessage(), 400);
        }
    }
}