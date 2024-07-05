<?php

require_once("AController.php");
require_once("services/VentaService.php");
require_once("helpers/TypeHelper.php");

class VentaController extends AController {
    private $ventaService;
    public function __construct() {
        $this->ventaService = new VentaService();
    }
    public function crearVenta($request, $response, $args) {
        try {

            $parametros = $request->getParsedBody();
            $usuario = $parametros["usuario"];
            $nombre = $parametros["nombre"];
            $tipo = $parametros["tipo"];
            $marca = $parametros["marca"];
            $stock = $parametros["stock"];
            
            $venta = $this->ventaService->crearVenta($usuario,$nombre,$tipo,$marca,$stock, $request->getUploadedFiles()["imagen"]);
            
            if (gettype($venta) == "string") {
                throw new Exception($venta);
            }
            $content = json_encode(array("venta" => $venta));
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function productosVendidosPorFecha($request, $response, $args) {
        try {

            $fecha = isset($args['fecha']) ? $args['fecha'] : TypeHelper::datetime_now()->sub(new DateInterval("P1D"));
            $cantidadProductos = $this->ventaService->cantidadProductosVendidosPorFecha($fecha);
    
            $content = json_encode(array("Cantidad de productos vendidos" => $cantidadProductos));
    
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function ventasPorUsuario($request, $response, $args) {
        try {

            $usuario = $request->getQueryParams()['usuario'];
    
            if (!isset($usuario)) {
                throw new Exception("Usuario no valido");
            }
    
            $ventas = $this->ventaService->ventasPorUsuario($usuario);
            $content = json_encode(array("ventas"=>$ventas));
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function ventasPorTipoProducto($request, $response, $args) {
        try {
            $tipo = $request->getQueryParams()['tipoProducto'];
            
            if (!isset($tipo)) {
                throw new Exception("Tipo no enviado.");
            }

            $ventas = $this->ventaService->ventasPorTipoProducto($tipo);
            $content = json_encode(array("ventas"=>$ventas));
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }

    }
    
    public function ventasEntreValores($request, $response, $args) {
        try {
            $queryParams = $request->getQueryParams();
            $precioDesde = $queryParams['precioDesde'];
            $precioHasta  = $queryParams['precioHasta'];

            if (!isset($precioDesde) || $precioDesde < 0 || !isset($precioHasta) || $precioHasta < 0) {
                throw new Exception("Alguno de los parametros enviados no es valido.");
            }
            $ventas = $this->ventaService->obtenerVentasPorRangoPrecios($precioDesde, $precioHasta);
            $content = json_encode(array("ventas"=>$ventas));
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function gananciasPorFecha($request, $response, $args) {
        try {
            $fecha = $request->getQueryParams()['fecha'];

            $cantidadDinero = $this->ventaService->obtenerCantidadGanancias($fecha);

            $content = json_encode(array("ganancias"=>$cantidadDinero));
            return $this->setearResponse($response, $content);

        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function productoMasVendido($request, $response, $args) {
        try {

            $producto = $this->ventaService->obtenerProductoMasVendido();
            $content = json_encode(array("producto"=>$producto));
            return $this->setearResponse($response, $content);

        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function modificarVenta($request, $response, $args) {
        try {

            $parametros = json_decode($request->getBody()->getContents(), true);
            $venta = $this->ventaService->modificarVenta($parametros);

            $content = json_encode(array("venta"=>$venta));
            return $this->setearResponse($response, $content);

        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function descargarVentas($request, $response, $args) {
        try {
            $archivo = $this->ventaService->exportarVentasACsv();
            $contenido = file_get_contents($archivo);
            unlink($archivo);
            
            return $this->setearResponse($response, $contenido, 'text/csv')->withHeader('Content-Disposition', 'attachment; filename="ventas.csv"');
        }
        catch (Exception $e) {
            return $this->setearResponseError($response, $e->getMessage(), 400);
        }
    }
    
    
    

}