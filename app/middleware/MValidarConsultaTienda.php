<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once("helpers/TypeHelper.php");

class MValidarConsultaTienda {


    public function __invoke($request, $handler): Response {
        try {
            $this->validarParametrosConsultaTienda($request->getParsedBody());
            $response = $handler->handle($request);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $ex) {
            throw new Exception("Se produjo un error: " . $ex->getMessage());
        }
    }

    private function validarParametrosConsultaTienda($parametros) {
        $nombre = $parametros['nombre'];
        $marca = $parametros['marca'];
        $tipo = $parametros['tipo'];

        if (!TypeHelper::str_has_value($nombre)) {
            throw new Exception("El nombre no fue proporcionado");
        }
        if (!TypeHelper::str_has_value($marca)) {
            throw new Exception("La marca no fue proporcionada");
        }
        if (!TypeHelper::str_has_value($tipo)) {
            throw new Exception("El tipo no fue proporcionado");
        }
    }


}