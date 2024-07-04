<?php

abstract class AController {
    private $directorioImagenes = "/ImagenesDe@/2024/";
    protected function setearResponse($response, $content) {
        $response->getBody()->write($content);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    protected function setearResponseError($response, $mensajeError, $codigo) {
        $content = json_encode(array("statusCode"=>$codigo, "error"=>$mensajeError));
        return $this->setearResponse($response, $content);
    }
}