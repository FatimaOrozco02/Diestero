<?php

declare(strict_types=1);

use Core\Router;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;
use Core\Config;

// Rutas sin autenticación
// $router->get("/", "HomeController@index");
// $router->get("/logout", "AuthController@logout");





/*** Público (sin auth) */
$router->get('', 'HomeController@home');
$router->get('certificaciones', 'HomeController@certifications');
$router->get('soluciones_estrategicas', 'HomeController@solutions');
$router->get('cfoaas', 'HomeController@cfoaas');
$router->get('socios', 'HomeController@partners');
$router->get('contacto', 'HomeController@contact');


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
                  $router->get('', 'AdminController@certifications');
                  $router->get('crear', 'CertificationController@create');
                  $router->get('/data', 'CertificationController@getTableData');
                  $router->get('/{certification_id}/actualizar', 'CertificationController@show');
                  $router->get('/{certification_id}/ver', 'CertificationController@view');
                  $router->get('/{certification_id}/descarga', 'CertificationController@download');
                  $router->get('/{certification_id}/data', 'CertificationController@getDetail');
                  $router->post('', 'CertificationController@store', [CsrfMiddleware::class]);
                  $router->put('/{certification_id}', 'CertificationController@update', [CsrfMiddleware::class]);
                  $router->delete('/{certification_id}', 'CertificationController@destroy', [CsrfMiddleware::class]);
            });

            // Cerrar sesión
            $router->get('/cerrar_sesion', 'AdminController@logout');
      });
});

// Archivos
$router->group(['prefix' => 'media'], function (Router $router) {
      $router->get('/certificaciones/{certification_id}', 'MediaController@certification');
      $router->get('/firmas/{certification_id}', 'MediaController@signature');
});

// Pruebas
if (Config::get('app.debug')) {
      $router->group(['prefix' => 'try/'], function (Router $router) {
            $router->get('password', 'TryController@password');
      });
}
