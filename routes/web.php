<?php

declare(strict_types=1);

use Core\Router;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;


// Rutas sin autenticación
$router->get("/", "HomeController@index");
$router->get("/logout", "AuthController@logout");





/*** Público (sin auth) */
$router->get('/somos', 'HomeController@home');
$router->get('/certificaciones', 'HomeController@certifications');
$router->get('/soluciones_estrategicas', 'HomeController@solutions');
$router->get('/cfoaas', 'HomeController@cfoaas');
$router->get('/socios', 'HomeController@partners');
$router->get('/contacto', 'HomeController@contact');


// Admin
$router->group(['prefix' => 'admin'], function (Router $router) {
      // Sin autenticar
      $router->group(['middlewares' => [GuestMiddleware::class]], function (Router $router) {
            $router->get('', 'AdminController@index');
            $router->post('/inicio_sesion', 'AdminController@login', [CsrfMiddleware::class]);
      });

      // Admin autenticado
      $router->group(['middlewares' => [AuthMiddleware::class]], function (Router $router) {
            // Certificados
            $router->group(['prefix' => '/certificaciones'], function (Router $router) {
                  $router->get('', 'CertificationController@index');
                  $router->get('/data', 'CertificationController@getTableData');
                  $router->get('/{certification_id}/data', 'CertificationController@getDetail');
                  $router->get('/{certification_id}/descarga', 'CertificationController@download');
                  $router->post('', 'CertificationController@store', [CsrfMiddleware::class]);
                  $router->put('/{certification_id}', 'CertificationController@update', [CsrfMiddleware::class]);
                  $router->delete('/{certification_id}', 'CertificationController@destroy', [CsrfMiddleware::class]);
            });

            // Cerrar sesión
            $router->post('/cerrar_sesion', 'AdminController@logout', [CsrfMiddleware::class]);
      });
});
