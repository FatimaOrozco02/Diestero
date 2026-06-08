<?php

declare(strict_types=1);

use Core\Log;

use Core\Router;
use App\Middlewares\CsrfMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\ProfileMiddleware;


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