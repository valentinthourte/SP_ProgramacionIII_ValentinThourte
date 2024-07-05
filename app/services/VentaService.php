<?php

require_once("services/AService.php");
require_once("helpers/StringHelper.php");
require_once("model/Venta.php");

class VentaService extends AService {

    private $productoService;
    public function __construct() {
        parent::__construct();
        $this->productoService = new ProductoService();
    }
    public function crearVenta($usuario,$nombre,$tipo,$marca,$stock, $imagen) {
        $this->validarParametrosVenta($usuario,$nombre,$tipo,$marca,$stock);
        $venta = $this->efectuarVentaSiEsPosible($usuario, $nombre, $tipo, $marca, $stock, $imagen);

        return $venta;

    }

    public function efectuarVentaSiEsPosible($usuario, $nombre, $tipo, $marca, $stock, $imagen) {
        $producto = $this->productoService->obtenerProductoPorNombreMarcaYTipo($nombre, $marca, $tipo);
        if ($this->productoService->puedeEfectuarVenta($producto, $stock)) {
            $venta = new Venta($usuario, $stock, $producto);
            $this->productoService->descontarStockProducto($producto, $stock);
            $ruta = $this->subirImagenDeEntidad($venta, $imagen);
            $venta->setearImagen($ruta);
            $venta->setId($this->guardarVenta($venta));
        }
        else {
            $venta = "La venta no se puede efectuar, el producto no existe o no hay suficiente stock";
        }
        return $venta;
    }

    function guardarVenta($venta) {
        return $this->crearEntidad($venta);
    }

    public function validarParametrosVenta($usuario,$nombre,$tipo,$marca,$stock) {
        if (StringHelper::isNullOrEmpty($usuario) ||
            StringHelper::isNullOrEmpty($nombre) ||
            !$this->tipoEsValido($tipo) ||
            StringHelper::isNullOrEmpty($marca) ||
            !is_numeric($stock) || $stock <= 0) {
            throw new Exception("parámetros inválidos");
        }
    }
    private function tipoEsValido($tipo) {
        
        $tipos = array("Smartphone" ,"Tablet");
        return !StringHelper::isNullOrEmpty($tipo) && in_array($tipo, $tipos);
    }

    public function cantidadProductosVendidosPorFecha($fecha) {
        $query = "SELECT SUM(cantidad) as cantidadProductosVendidos FROM Venta WHERE fecha > :fecha";
        $fechaString = $fecha->format('Y-m-d H:i:s');
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":fecha", $fechaString, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC)['cantidadProductosVendidos'];
    }

    public function ventasPorUsuario($usuario) {
        $query = Venta::obtenerConsultaSelect() . " WHERE usuario = :usuario";

        $consulta = $this->accesoDatos->prepararConsulta($query);

        $consulta->bindValue(":usuario", $usuario);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Venta::class);
    }

    public function ventasPorTipoProducto($tipo) {
        if (!$this->tipoEsValido($tipo)) {
            throw new Exception("El tipo enviado no es un tipo valido.");
        }
        $query = Venta::obtenerConsultaSelect() . " WHERE tipo= :tipo";

        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":tipo", $tipo);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, Venta::class);
    }

    public function obtenerVentasPorRangoPrecios($precioDesde, $precioHasta) {
        $query = Venta::obtenerConsultaSelect() . " WHERE precioTotal >= :precioDesde AND precioTotal <= :precioHasta";
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":precioDesde", $precioDesde);
        $consulta->bindValue(":precioHasta", $precioHasta);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, Venta::class);
    }

    public function obtenerCantidadGanancias($fecha) {
        $query = isset($fecha) ?  "SELECT SUM(precioTotal) as ganancia from Venta WHERE DATE(fecha) = :fecha" : "SELECT SUM(precioTotal) as ganancia from Venta";
        $consulta = $this->accesoDatos->prepararConsulta($query);
        if (isset($fecha)) {
            $consulta->bindValue(":fecha", $fecha);
        }
        $consulta->execute();

        
        return $consulta->fetch(PDO::FETCH_ASSOC)['ganancia'];
    }

    public function obtenerProductoMasVendido() {
        $query = "SELECT nombre, marca, tipo, SUM(cantidad) AS total_cantidad from Venta GROUP BY nombre, marca, tipo ORDER BY total_cantidad DESC LIMIT 1";

        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->execute();

        $datos = $consulta->fetch(PDO::FETCH_ASSOC);
        $producto = $this->productoService->obtenerProductoPorNombreMarcaYTipo($datos['nombre'],$datos['marca'],$datos['tipo']);

        return $producto;
    }

    public function modificarVenta($parametros) {
        $numeroPedido =$parametros['numeroPedido'];
        $usuario =$parametros['usuario'];
        $nombre =$parametros['nombre'];
        $marca =$parametros['marca'];
        $tipo =$parametros['tipo'];
        $cantidad =$parametros['cantidad'];

        $producto = $this->productoService->obtenerProductoPorNombreMarcaYTipo($nombre, $marca, $tipo);
        if (!$producto) {
            throw new Exception("El nuevo producto no existe.");
        }
        $venta = $this->obtenerVentaPorNumeroPedido($numeroPedido);
                
        if ($venta) {
            $venta = $this->actualizarVenta($numeroPedido,$usuario,$nombre,$marca,$tipo,$cantidad);
            return $venta;
        }
        else {
            throw new Exception("No existe pedido con ese número");
        }
    }

    public function exportarVentasACsv() {
        $nombreArchivo = tempnam(sys_get_temp_dir(), 'ventas_:');
        $csv = fopen($nombreArchivo, "w");
        
        
        fputcsv($csv, Venta::obtenerCabecerasCSV());

        $ventas = $this->obtenerVentas();

        foreach($ventas as $venta) {
            fputcsv($csv, $venta->toCsv());
        }
        fclose($csv);
        return $nombreArchivo;
    }

    public function obtenerVentas() {
        $query = Venta::obtenerConsultaSelect() ;

        $consulta = $this->accesoDatos->prepararConsulta($query);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Venta::class);
    }

    private function actualizarVenta($numeroPedido,$usuario,$nombre,$marca,$tipo,$cantidad) {
        $query = "UPDATE Venta SET usuario = :usuario, nombre = :nombre, marca = :marca, tipo = :tipo, cantidad = :cantidad WHERE numeroPedido = :numeroPedido";
        
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $valores = [':usuario' => $usuario, ':nombre' => $nombre, ':marca' => $marca, ':tipo' => $tipo, ':cantidad' => $cantidad, ':numeroPedido' => $numeroPedido];
        $consulta = $this->bindearValores($consulta, $valores);

        $consulta->execute();

        return $this->obtenerVentaPorNumeroPedido($numeroPedido);
    }

    private function obtenerVentaPorNumeroPedido($numero) {
        $query = "SELECT * FROM Venta WHERE numeroPedido = :numeroPedido";
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":numeroPedido", $numero);
        $consulta->execute();

        return $consulta->fetchObject(Venta::class);
    }
}