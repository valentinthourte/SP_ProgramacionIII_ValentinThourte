<?php

abstract class AController {
    private $directorioImagenes = "/ImagenesDe@/2024/";
    protected function setearResponse($response, $content, $contentType = 'application/json') {
        $response->getBody()->write($content);
        return $response
            ->withHeader('Content-Type', $contentType);
    }
    protected function setearResponseError($response, $mensajeError, $codigo) {
        $content = json_encode(array("statusCode"=>$codigo, "error"=>$mensajeError));
        return $this->setearResponse($response, $content);
    }
}