<?php

require_once("interface/IEntity.php");
require_once("helpers/TypeHelper.php");
class Usuario implements IEntity, JsonSerializable {
    private $id;
    private $mail;
    private $usuario;
    private $contrasenia;
    private $perfil;
    private $rutaImagen;
    private $fechaAlta;
    private $fechaBaja;


    function __construct()
	{
		$params = func_get_args();
        
		$num_params = func_num_args();
        
		$funcion_constructor ='__construct'.$num_params;
        
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
	}
    public function __construct4($mail, $usuario, $contrasenia, $perfil) {
        $this->mail = $mail;
        $this->usuario = $usuario;
        $this->contrasenia = $contrasenia;
        $this->perfil = $perfil;
        $this->fechaAlta = TypeHelper::datetime_now(); 
        $this->rutaImagen = null;
        $this->fechaBaja = null;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'mail' => $this->mail,
            'usuario' => $this->usuario,
            'contrasenia' => $this->contrasenia,
            'perfil' => $this->perfil,
            'rutaImagen' => $this->rutaImagen,
            'fechaAlta' => $this->fechaAlta,
            'fechaBaja' => $this->fechaBaja
        ];
    }

    
    public function obtenerPerfil() {
        return $this->perfil;
    }
    public function obtenerNombreUsuario() {
        return $this->usuario;
    }
    public function asignarId($id) {
        $this->id = $id;
    }
    public function asignarImagen($rutaImagen) {
        $this->rutaImagen = $rutaImagen;
    }

    public function valoresInsert() {
        return array(
            ':id' => $this->id,
            ':mail' => $this->mail,
            ':usuario' => $this->usuario,
            ':contrasenia' => $this->contrasenia,
            ':perfil' => $this->perfil,
            ':rutaImagen' => $this->rutaImagen,
            ':fechaAlta' => $this->fechaAlta->format("Y-m-d H:i:s"),
            ':fechaBaja' => $this->fechaBaja
        );
    }
    public function obtenerNombreImagen() {
        return $this->usuario . $this->perfil . TypeHelper::datetime_now()->format("Y-m-d");
    }
    public static function obtenerConsultaInsert() {
        return "INSERT INTO Usuarios (mail, usuario, contrasenia, perfil, foto, fechaAlta) VALUES (:mail,:usuario,:contrasenia,:perfil,:rutaImagen,:fechaAlta)";
    }
    public static function obtenerConsultaSelect() {
        throw new Exception("Usuario.php: No implementado");
    }
    public static function obtenerConsultaSelectPorId() {
        throw new Exception("Usuario.php: No implementado");
    }
    public static function obtenerConsultaDeletePorId() {
        throw new Exception("Usuario.php: No implementado");
    }
}