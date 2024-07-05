<?php

require_once("services/AService.php");
require_once("model/Usuario.php");
require_once("jwt/JWTHelper.php");
class LoginService extends AService {
    

    public function loguearUsuario($parametros) {
        $nombreUsuario = $parametros['usuario'];
        $contrasenia = $parametros['contrasenia'];

        $usuario = $this->obtenerUsuario($nombreUsuario, $contrasenia);

        if ($usuario) {
            return $this->generarToken($usuario->obtenerNombreUsuario(), $usuario->obtenerPerfil());
        }
        else {
            throw new Exception("Credenciales incorrectas.");
        }
    }

    private function obtenerUsuario($nombre, $contrasenia) {
        $query = "SELECT * FROM Usuarios WHERE usuario = :usuario AND contrasenia = :contrasenia";
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":usuario", $nombre);
        $consulta->bindValue(":contrasenia", $contrasenia);

        $consulta->execute();

        return $consulta->fetchObject(Usuario::class);
    }

    private function generarToken($nombreUsuario, $perfil) {
        $data = array('nombreUsuario'=>$nombreUsuario, "perfil"=>$perfil);
        $token = JWTHelper::crearToken($data);
        return array("token"=>$token);
    }

}