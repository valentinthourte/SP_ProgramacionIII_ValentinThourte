<?php

require_once("AController.php");
require_once("services/ProductoService.php");
require_once("model/Producto.php");
class TiendaController extends AController {
    private $productoService;
    public function __construct() {
        $this->productoService = new ProductoService();
    }
    public function crearUno($request, $response, $args) {
        try { 
            $parametros = $request->getParsedBody();
            $producto = new Producto($parametros['nombre'],$parametros['precio'],$parametros['tipo'],$parametros['marca'],$parametros['stock']);
            Producto::validarProducto($producto);
            $producto = $this->productoService->crearProducto($producto, $request->getUploadedFiles()['imagen']);

            $content = json_encode(array("producto" => $producto, "mensaje"=>"Producto creado con éxito."));

            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 400);
        }

    }

    public function existePorMarcaYTipo($request, $response, $args) {
        $parametros = $request->getParsedBody();
        $nombre = $parametros["nombre"];
        $marca = $parametros["marca"];
        $tipo = $parametros["tipo"];

        try {

            $mensaje = $this->productoService->productoExiste($nombre, $marca, $tipo);
            return $this->setearResponse($response, $mensaje);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }

    public function leerTodos($request, $response, $args) {
        try {
            $productos = $this->productoService->leerProductos();
            $content = array("productos"=>$productos);
            return $this->setearResponse($response, $content);
        }
        catch (Exception $ex) {
            return $this->setearResponseError($response, $ex->getMessage(), 404);
        }
    }
    public function leerUno($request, $response, $args) {
        throw new Exception("No implementado");
    }
    public function actualizar($request, $response, $args) {
        throw new Exception("No implementado");
    }
    public function eliminar($request, $response, $args) {
        throw new Exception("No implementado");
    }
}
