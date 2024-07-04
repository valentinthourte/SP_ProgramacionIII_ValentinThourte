<?php

require_once("interface/IEntity.php");
require_once("helpers/StringHelper.php");

class Producto implements IEntity, JsonSerializable {

    private $id;
    private $nombre;
    private $precio;
    private $tipo;
    private $marca;
    private $stock;

    function __construct()
	{
		$params = func_get_args();
        
		$num_params = func_num_args();
        
		$funcion_constructor ='__construct'.$num_params;
        
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
	}
    public function __construct4($nombre, $precio, $tipo, $marca) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->stock = 0;
    }
    public function __construct5($nombre, $precio, $tipo, $marca, $stock) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->stock = (int)$stock;
    }
    public function __construct6($id, $nombre, $precio, $tipo, $marca, $stock) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->stock = (int)$stock;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'tipo' => $this->tipo,
            'marca' => $this->marca,
            'stock' => $this->stock
        ];
    }
    
    public function obtenerNombreImagen() {
        return $this->nombre . "-" . $this->tipo;
    }

    public function valoresInsert() {
        return array(":nombre"=>$this->nombre,":precio"=>$this->precio, ":stock"=>$this->stock, ":marca"=>$this->marca, ":tipo"=>$this->tipo);
    }
    public static function obtenerConsultaInsert() {
        return "INSERT INTO Producto(nombre,precio,stock,marca,tipo) VALUES (:nombre,:precio,:stock,:marca,:tipo)";
    }
    public static function obtenerConsultaSelect() {
        return "SELECT * FROM Producto";
    }
    public static function obtenerConsultaSelectPorId() {
        return Producto::obtenerConsultaSelect() . " WHERE Id = :id";
    }
    public static function obtenerConsultaDeletePorId() {
        return "DELETE FROM Producto WHERE Id = :id";
    }

    public static function obtenerConsultaSelectPorNombreMarcaYTipo() {
        return Producto::obtenerConsultaSelect() . " WHERE Nombre = :nombre and tipo = :tipo and marca = :marca";
    }

    public function bindearConsultaSelectNombreYTipo($consulta) {
        $consulta->bindValue(":nombre", $this->nombre);
        $consulta->bindValue(":tipo", $this->tipo);

        return $consulta;
    }

    function coincide_marca($marca) {
        return $this->marca == $marca;
    }
    function coincide_tipo($tipo) {
        return $this->tipo == $tipo;
    }
    public function obtenerConsultaUpdateStock() {
        return "UPDATE Producto SET stock = (:stock) WHERE id = :id";
    }

    public function bindearConsultaUpdateStock($consulta) {
        $consulta->bindValue(":stock", $this->stock);
        $consulta->bindValue(":id", $this->id);

        return $consulta;
    }


    public function actualizarStock(Producto $producto) {
        $this->stock = $this->actualizarStockNumerico($producto->stock);
    }

    public function actualizarStockNumerico($stock) {
        return (int)$this->stock + (int)$stock;
    }

    public function tieneStockSuficiente($stock) {
        return (int)$this->stock >= (int)$stock;
    }

    public static function validarProducto($producto) {
        $tipos = array("Smartphone" ,"Tablet");
        if (StringHelper::isNullOrEmpty($producto->nombre) 
        || (StringHelper::isNullOrEmpty($producto->tipo) || !in_array($producto->tipo, $tipos))
        || StringHelper::isNullOrEmpty($producto->marca) 
            || !is_numeric($producto->precio) || $producto->precio <= 0 
            || !is_numeric($producto->stock) || $producto->stock <= 0) 
        {
            throw new Exception("Datos no válidos");
        }
    }   

    public function obtenerId()
    {
        return $this->id;
    }

    public function obtenerNombre()
    {
        return $this->nombre;
    }

    public function obtenerPrecio()
    {
        return $this->precio;
    }

    public function obtenerTipo()
    {
        return $this->tipo;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function obtenerMarca()
    {
        return $this->marca;
    }

    public function obtenerStock()
    {
        return $this->stock;
    }
    
}