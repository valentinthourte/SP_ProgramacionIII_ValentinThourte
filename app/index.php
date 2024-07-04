<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require_once("controllers/TiendaController.php");
require_once("controllers/VentaController.php");
require_once '../vendor/autoload.php';


date_default_timezone_set("America/Argentina/Buenos_Aires");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();


$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->get('[/]', \TiendaController::class . ':leerTodos');
  $group->post('/consultar', \TiendaController::class . ':existePorMarcaYTipo');
  $group->post('/alta', \TiendaController::class . ':crearUno');
});

$app->group("/ventas", function (RouteCollectorProxy $group) {
    $group->post('/alta', \VentaController::class . ':crearVenta');
    $group->put('/modificar', \VentaController::class . ':modificarVenta');
});

$app->group("/ventas/consultar", function (RouteCollectorProxy $group) {
    $group->get('/productos/vendidos', \VentaController::class . ":productosVendidosPorFecha");
    $group->get('/ventas/porUsuario', \VentaController::class . ":ventasPorUsuario");
    $group->get('/ventas/porProducto', \VentaController::class . ":ventasPorTipoProducto");
    $group->get('/productos/entreValores', \VentaController::class . ":ventasEntreValores");
    $group->get('/ventas/ingresos', \VentaController::class . ":gananciasPorFecha");
    $group->get('/productos/masVendido', \VentaController::class . ":productoMasVendido");
});


$app->run();