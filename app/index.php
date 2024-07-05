<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require_once("controllers/TiendaController.php");
require_once("controllers/VentaController.php");
require_once("controllers/UsuarioController.php");
require_once("controllers/LoginController.php");


require_once("middleware/MConfirmarPerfil.php");
require_once("middleware/MValidarConsultaTienda.php");

require_once '../vendor/autoload.php';


date_default_timezone_set("America/Argentina/Buenos_Aires");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->group('/tienda', function (RouteCollectorProxy $group) {

  $group->get('[/]', \TiendaController::class . ':leerTodos');

  $group->post('/consultar', \TiendaController::class . ':existePorMarcaYTipo')
  ->add(new MValidarConsultaTienda());

  $group->post('/alta', \TiendaController::class . ':crearUno')
  ->add(new ConfirmarPerfil(array("admin")));

});

$app->group("/ventas", function (RouteCollectorProxy $group) {

    $group->post('/alta', \VentaController::class . ':crearVenta')
    ->add(new ConfirmarPerfil(array("admin", "empleado")));

    $group->put('/modificar', \VentaController::class . ':modificarVenta')
    ->add(new ConfirmarPerfil(array("admin")));

    $group->get('/descargar', \VentaController::class . ':descargarVentas')
    ->add(new ConfirmarPerfil(array("admin")));

});

$app->group("/registro", function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . ':registrarUsuario');
});

$app->post('/login', \LoginController::class . ':loginUsuario');

$app->group("/ventas/consultar", function (RouteCollectorProxy $group) {

    $group->get('/productos/vendidos', \VentaController::class . ":productosVendidosPorFecha");

    $group->get('/ventas/porUsuario', \VentaController::class . ":ventasPorUsuario");

    $group->get('/ventas/porProducto', \VentaController::class . ":ventasPorTipoProducto");

    $group->get('/productos/entreValores', \VentaController::class . ":ventasEntreValores");

    $group->get('/ventas/ingresos', \VentaController::class . ":gananciasPorFecha")
    ->add(new ConfirmarPerfil(array("admin")));

    $group->get('/productos/masVendido', \VentaController::class . ":productoMasVendido");
})
->add(new ConfirmarPerfil(array("admin", "empleado")));

$app->run();