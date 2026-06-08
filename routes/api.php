<?php

declare(strict_types=1);

use App\Middlewares\crm_gdc\AuthMiddleware;
use Core\Router;

$router->group(["prefix" => "/api", "middlewares" => [AuthMiddleware::class]], function (Router $router) {
   $router->get("/sections", "ApiController@getSections");
});
