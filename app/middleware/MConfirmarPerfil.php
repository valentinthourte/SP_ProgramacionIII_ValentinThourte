<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once("jwt/JWTHelper.php");

class ConfirmarPerfil {
    private $perfiles;

    public function __construct($perfiles) {
        $this->perfiles = $perfiles;
    }

    public function __invoke($request, $handler): Response {
        try {
            $token = $this->obtenerToken($request);
            JWTHelper::verificarToken($token);
            if (!$this->perfilEsValido($token)) {
                throw new Exception("Tipo de usuario no valido");
            }
            $response = $handler->handle($request);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $ex) {
            throw new Exception("Se produjo un error: " . $ex->getMessage());
        }
    }

    private function obtenerToken(Request $request): string {
        $header = $request->getHeaderLine('Authorization');

        if (!$header) {
            throw new Exception('No se recibio el token');
        }

        $partes = explode("Bearer", $header);
        if (count($partes) < 2) {
            throw new Exception('Formato de token invalido');
        }

        return trim($partes[1]);
    }

    private function perfilEsValido($token) {
        try {
            $datos = JWTHelper::obtenerData($token);
            $perfil = $datos->perfil;
            return in_array($perfil, $this->perfiles);
        }
        catch (ValueError $ex) {
            return false;
        }
    }

}