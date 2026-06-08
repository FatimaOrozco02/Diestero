<?php

namespace Core;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Singleton Logger class
final class Log {
    private static ?Logger $logger = null;

    /* 
     * Crea o retorna una instancia de Logger configurada para escribir en un archivo de log diario.
     * El archivo de log se encuentra en storage/logs/ y se nombra con la fecha actual (YYYY-MM-DD.log).
     */
    public static function createLog(): Logger {
        date_default_timezone_set("America/Mexico_City");

        if (self::$logger === null) {
            self::$logger = new Logger("app");

            if (!is_dir(__DIR__ . "/../storage/logs")) {
                mkdir(__DIR__ . "/../storage/logs/", 0777, true);
            }

            if (!file_exists(__DIR__ . "/../storage/logs/" . date("Y-m-d") . ".log")) {                
                touch(__DIR__ . "/../storage/logs/" . date("Y-m-d") . ".log");                
            }

            self::$logger->pushHandler(new StreamHandler(__DIR__ . "/../storage/logs/" . date("Y-m-d") . ".log", Level::Debug));
        }

        return self::$logger;
    }
}