<?php

declare(strict_types = 1);

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir === '/') {
      $scriptDir = '';
}
$scriptDir = preg_replace('#/public$#', '', $scriptDir);
$subdomainName = explode('.', $host)[0];

return [
    "name"     => "Diestro",
    "env"      => "local",
    "debug"    => false,
    'url'      => "$protocol://$host$scriptDir/",
    "timezone" => "America/Mexico_City",
    "session_name" => "diestro",
    "key"      => "diestro_key",
];