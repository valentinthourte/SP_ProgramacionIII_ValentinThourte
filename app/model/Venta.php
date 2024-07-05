<?php

require_once("interface/IEntity.php");
require_once("helpers/TypeHelper.php");
class Venta implements IEntity, JsonSerializable {

    private $id;
    private $usuario;
    private $nombre;
    private $marca;
    private $tipo;
    private $cantidad;
    private $fecha;
    private $numeroPedido;
    private $precioTotal;
    private $rutaImagen;


    function __construct()
	{
		$params = func_get_args();
        
		$num_params = func_num_args();
        
		$funcion_constructor ='__construct'.$num_params;
        
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
	}

    public function __construct3($usuario, $cantidad, Producto $producto) {
        $this->id = null;
        $this->usuario = $usuario;
        $this->nombre = $producto->obtenerNombre();
        $this->tipo = $producto->obtenerTipo();
        $this->marca = $producto->obtenerMarca();
        $this->cantidad = $cantidad;
        $this->precioTotal = $cantidad * $producto->obtenerPrecio();
        $this->numeroPedido = Venta::generarNumeroPedido();
        $this->fecha = TypeHelper::datetime_now_string();
    }

    public function __construct6($usuario, $nombre, $marca, $tipo, $cantidad, $precioTotal) {
        $this->id = null;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->cantidad = $cantidad;
        $this->precioTotal = $precioTotal;
        $this->numeroPedido = Venta::generarNumeroPedido();
        $this->rutaImagen = "Sin imagen";
        $this->fecha = TypeHelper::datetime_now_string();
    }
    public function __construct7($usuario, $nombre, $marca, $tipo, $cantidad, $precioTotal, $fecha) {
        $this->id = null;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->cantidad = $cantidad;
        $this->precioTotal = $precioTotal;
        $this->numeroPedido = Venta::generarNumeroPedido();
        $this->fecha = $fecha;
        $this->rutaImagen = "Sin imagen";
    }
    
    public function __construct8($usuario, $nombre, $marca, $tipo, $cantidad, $precioTotal, $fecha, $nroPedido) {
        $this->id = null;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->cantidad = $cantidad;
        $this->precioTotal = $precioTotal;
        $this->numeroPedido = $nroPedido;
        $this->fecha = $fecha;
        $this->rutaImagen = "Sin imagen";
    }
    public function __construct9($id, $usuario, $nombre, $marca, $tipo, $cantidad, $precioTotal, $fecha, $nroPedido) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->cantidad = $cantidad;
        $this->precioTotal = $precioTotal;
        $this->numeroPedido = $nroPedido;
        $this->fecha = $fecha;
        $this->rutaImagen = "Sin imagen";
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'usuario' => $this->usuario,
            'nombre' => $this->nombre,
            'marca' => $this->marca,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'fecha' => $this->fecha,
            'numeroPedido' => $this->numeroPedido,
            'precioTotal' => $this->precioTotal,
            'rutaImagen' => $this->rutaImagen
        ];
    }
    
    public function setearImagen($ruta) {
        $this->rutaImagen = $ruta;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public static function obtenerCabecerasCSV() {
        return [
            'ID',
            'Usuario',
            'Nombre',
            'Marca',
            'Tipo',
            'Cantidad',
            'Fecha',
            'Número de Pedido',
            'Precio Total',
            'Ruta de Imagen'
        ];
    }

    public function toCsv() {
        return [
            $this->id,
            $this->usuario,
            $this->nombre,
            $this->marca,
            $this->tipo,
            $this->cantidad,
            $this->fecha,
            $this->numeroPedido,
            $this->precioTotal,
            $this->rutaImagen
        ];
    }
    
    public function valoresInsert() {
        return array(
            ":usuario" => $this->usuario,
            ":nombre" => $this->nombre,
            ":marca" => $this->marca,
            ":tipo" => $this->tipo,
            ":cantidad" => $this->cantidad,
            ":fecha" => $this->fecha,
            ":numeroPedido" => $this->numeroPedido,
            ":precioTotal" => $this->precioTotal,
            ":rutaImagen" => $this->rutaImagen
        );
    }
    public function obtenerNombreImagen() {
        return $this->nombre . $this->tipo . $this->marca . $this->obtenerNombreUsuarioImagen();
    }
    public static function obtenerConsultaInsert() {
        return "INSERT INTO Venta(usuario, nombre, marca, tipo, cantidad, precioTotal, fecha, numeroPedido, rutaImagen) VALUES (:usuario,:nombre,:marca,:tipo,:cantidad,:precioTotal,:fecha,:numeroPedido, :rutaImagen)";
    }
    public static function obtenerConsultaSelect() {
        return "SELECT * FROM Venta";
    }
    public static function obtenerConsultaSelectPorId() {
        return Venta::obtenerConsultaSelect() . " WHERE ID = :id";
    }
    public static function obtenerConsultaDeletePorId() {
        throw new Exception("No implementado");
    }


    function obtenerNombreUsuarioImagen() {
        $posicionArroba = strpos($this->usuario, '@');
        if ($posicionArroba !== false) {
            return substr($this->usuario, 0, $posicionArroba);
        } 
        else {
            return $this->usuario;
        }
    }
    private static function generarNumeroPedido() {
        $tamaño = 5;
        $numeroPedido = "";
        $caracteres = "abcdefghijklmnopqrstuvwxyz0123456789";
        for($i = 0; $i < $tamaño; $i++) {
            $numeroPedido = $numeroPedido . substr(str_shuffle($caracteres), 0, 1);
        }
        return $numeroPedido;
    }
}