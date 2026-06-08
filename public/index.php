<?php

declare(strict_types=1);

use Core\Config;
use Core\Database;
use Core\Log;
use Core\Request;
use Core\Response;
use Core\Router;

require_once __DIR__ . "/../vendor/autoload.php";

$config = [];

foreach (glob(dirname(__DIR__) . "/config/*.php") as $file) {
   $key = basename($file, ".php");
   $config[$key] = require $file;
}

$localFile = dirname(__DIR__) . "/local.php";
if (file_exists($localFile)) {
   $localConfig = require $localFile;

   foreach ($localConfig as $section => $values) {
      if (!isset($config[$section]) || !is_array($values)) {
         continue;
      }

      $config[$section] = array_replace_recursive($config[$section], $values);
   }
}

$GLOBALS["config"] = $config;

/*
|--------------------------------------------------------------------------
| Error handling & timezone
|--------------------------------------------------------------------------
*/
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

if (Config::get('app.debug', false) === true) {
   ini_set('display_errors', '1');
   ini_set('display_startup_errors', '1');
   error_reporting(E_ALL);
} else {
   ini_set('display_errors', '0');
   ini_set('display_startup_errors', '0');
   error_reporting(0);
}

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
| Sesión necesaria para CSRF, flash messages, auth, etc.
*/

$sessionName = Config::get("app.session_name");

if (session_status() !== PHP_SESSION_ACTIVE) {
   session_name($sessionName);
   session_start();
}

// Router
$request = Request::capture();
$response = new Response();

$router = new Router($request, $response);

// Rutas
require __DIR__ . "/../routes/web.php";
require __DIR__ . "/../routes/api.php";

$router->dispatch();
