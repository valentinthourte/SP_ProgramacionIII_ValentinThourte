<?php

require_once("services/AService.php");
require_once("model/Usuario.php");
class UsuarioService extends AService {


    public function crearUsuario($parametros, $imagen) {
        $this->validarParametrosAlta($parametros, $imagen);
        $usuario = new Usuario($parametros['mail'],$parametros['usuario'],$parametros['contrasenia'],$parametros['perfil']);
        $rutaImagen = $this->subirImagenDeEntidad($usuario, $imagen);
        $usuario->asignarImagen($rutaImagen);
        $usuario->asignarId($this->crearEntidad($usuario));
        return $usuario;
    }

    private function validarParametrosAlta($parametros, $imagen) {
        $perfiles = array("cliente", "empleado", "admin");
        if (    empty($parametros['mail'])
            ||  empty($parametros['usuario'])
            ||  empty($parametros['contrasenia'])
            ||  (empty($parametros['perfil']) && in_array($parametros['perfil'], $perfiles))
            ||  $imagen->getError() !== UPLOAD_ERR_OK) {
                throw new Exception("Los parametros o la imagen enviados no son validos. ");
            }
    }
}