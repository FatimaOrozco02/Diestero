<?php

declare(strict_types = 1);

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir  = rtrim(rtrim(str_replace(basename($scriptName), '', $scriptName), '/'), 'public/');

return [
    "name" => "diestro",
    "env" => "local",
    "debug" => false,
    "url" => "{$protocol}://{$host}{$scriptDir}",
    "timezone" => "America/Mexico_City",
    "session_name" => "crm_gdc_session",
    "key" => "crm_gdc_session"
];