<?php

require_once("../db/AccesoDatos.php");

abstract class AService {
    
    private $directorioImagenes = "/ImagenesDe@/2024/";
    protected AccesoDatos $accesoDatos;
    public function __construct() {
        $this->accesoDatos = AccesoDatos::obtenerInstancia();
    }

    protected function crearEntidad(IEntity $entidad) {
        $consulta = $this->accesoDatos->prepararConsulta($entidad->obtenerConsultaInsert());

        $consulta = $this->bindearValores($consulta, $entidad->valoresInsert());
        try {
            $consulta->execute();
        }
        catch(PDOException $e) {
            
            if (str_contains($e->getMessage(), "Duplicate entry")){
                throw new Exception("Error al insertar en base de datos: registro duplicado.-" . $e->getMessage());
            }
            else 
                throw $e;
        }
        return $this->accesoDatos->obtenerUltimoId();
    }

    protected function ejecutarConsultaBindeandoId($query, $id) {
        $TEXTO_BINDEAR_ID = ":id";
        if(str_contains($query, $TEXTO_BINDEAR_ID)) {
            $consulta = $this->accesoDatos->prepararConsulta($query);
            $consulta->bindValue($TEXTO_BINDEAR_ID, $id);
            $consulta->execute();
            return $consulta;
        }
        else {
            throw new Exception("La entidad debe utilizar ':id' para bindear el ID." . PHP_EOL);
        }
    }

    protected function subirImagenDeEntidad(IEntity $entidad, $imagen) {
        try {
            if ($imagen->getError() == UPLOAD_ERR_OK) {
                $rutaCompleta = $this->obtenerRutaImagenParaEntidad($entidad, $imagen);
                if(!file_exists(dirname($rutaCompleta))) {
                    mkdir(dirname($rutaCompleta), 0777, true);
                }
                $imagen->moveTo($rutaCompleta);
                return $rutaCompleta;
            }
            else {
                throw new Exception($imagen->getError());
            }
        }
        catch (Exception $ex) {
            echo "No se pudo subir la imagen: " . $ex->getMessage();
        }
    }

    protected function bindearValores($consulta, $valores) {
        foreach($valores as $key=>$value) {
            $consulta->bindValue($key, $value, PDO::PARAM_STR);
        }
        return $consulta;
    }
    protected function obtenerRutaImagenParaEntidad(IEntity $entidad, $imagen) {
        if ($imagen->getError() == UPLOAD_ERR_OK) {
            $nombre = str_replace(" ", "", $entidad->obtenerNombreImagen());
            $extension = pathinfo($imagen->getClientFilename(), PATHINFO_EXTENSION);
            $directorio = getcwd() . str_replace("@", $entidad::class,$this->directorioImagenes);
            $rutaCompleta = $directorio . $nombre . "." . $extension;
        }

        return $rutaCompleta;
    }

}