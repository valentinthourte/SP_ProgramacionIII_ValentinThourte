<?php

require_once("AController.php");
require_once("services/UsuarioService.php");
class UsuarioController extends AController {

    private $usuarioService;

    public function __construct() {
        $this->usuarioService = new UsuarioService();
    }

    public function registrarUsuario($request, $response, $args) {
        try {
            $parametros = $request->getParsedBody();
            $imagen = $request->getUploadedFiles()['imagen'];
            $usuario = $this->usuarioService->crearUsuario($parametros, $imagen);

            $content = json_encode($usuario);
            return $this->setearResponse($response, $content);

        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }
}