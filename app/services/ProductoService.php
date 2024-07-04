<?php

require_once("services/AService.php");

class ProductoService extends AService {

    function crearProducto($producto, $imagen) {
        $productoBd = $this->obtenerProducto($producto);
        if (!$productoBd) {
            $id = $this->crearEntidad($producto);
            $this->subirImagenDeEntidad($producto, $imagen);
            $producto->setId($id);
        }
        else {
            $productoBd->actualizarStock($producto);
            $this->actualizarStockProducto($productoBd);
        }
        return $productoBd ? $productoBd : $producto;
    }

    function obtenerProducto(Producto $producto) {
        return $this->obtenerProductoPorNombreMarcaYTipo($producto->obtenerNombre(), $producto->obtenerMarca(), $producto->obtenerTipo());
    }


    function obtenerProductoPorNombreMarcaYTipo($nombre, $marca, $tipo) {

        $query = Producto::obtenerConsultaSelectPorNombreMarcaYTipo();
        $consulta = $this->accesoDatos->prepararConsulta($query);
        $consulta->bindValue(":nombre", $nombre);
        $consulta->bindValue(":marca", $marca);
        $consulta->bindValue(":tipo", $tipo);

        $consulta->execute();

        return $consulta->fetchObject(Producto::class);
    }

    private function actualizarStockProducto(Producto $producto) {
        $query = $producto->obtenerConsultaUpdateStock();
        $consulta = $this->accesoDatos->prepararConsulta($query);

        $consulta = $producto->bindearConsultaUpdateStock($consulta);

        $consulta->execute();
    }


    function descontarStockProducto(Producto $producto, $stock) {
        $producto->actualizarStockNumerico(-$stock);
        $this->actualizarStockProducto($producto);
    }
    public function puedeEfectuarVenta($producto, $stock) {
        return isset($producto) && $producto->tieneStockSuficiente($stock);
    }

    public function productoExiste($nombre, $marca, $tipo) {
        $producto = $this->obtenerProductoPorNombreMarcaYTipo($nombre, $marca, $tipo);
        if (!$producto) {
            return $this->existeProductoPorMarcaYTipo($marca, $tipo);
        }
        return "Existe";
    }
    public function existeProductoPorMarcaYTipo($marca, $tipo) {
        $productos = $this->leerProductos();
        $mensaje = "";
        $tipo_estaba = false;
        $marca_estaba = false;
        $nombre_estaba = false;

        foreach($productos as $indice=>$producto) {
            if ($producto->coincide_tipo($tipo)) {
                $tipo_estaba = true;  
            }
            else if ($producto->coincide_marca($marca)) {
                $marca_estaba = true;
            }
        }
        if (!$marca_estaba) 
        $mensaje = $mensaje . "Ningun producto tiene esa marca." . PHP_EOL;
        if (!$tipo_estaba) {
            $mensaje = $mensaje . "Ningun producto tiene ese tipo." . PHP_EOL;
        }
        return $mensaje;
    }

    public function leerProductos() {
        $query = Producto::obtenerConsultaSelect();

        $consulta = $this->accesoDatos->prepararConsulta($query);

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Producto::class);
    }

}