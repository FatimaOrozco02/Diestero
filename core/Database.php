<?php

declare(strict_types = 1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

final class Database
{
   /**
    * @var array<string, PDO>
    */
   private static array $connections = [];

   public static function connection(?string $name = null): PDO
   {
      $name ??= self::getDefaultConnectionName();

      if (isset(self::$connections[$name])) {
         return self::$connections[$name];
      }

      $config = self::getConnectionConfig($name);
      $pdo = self::createPdo($config);

      self::$connections[$name] = $pdo;

      return $pdo;
   }

   public static function query(string $sql, array $params = [], ?string $connection = null): PDOStatement
   {
      $stmt = self::connection($connection)->prepare($sql);
      $stmt->execute($params);

      return $stmt;
   }

   public static function fetchAll(string $sql, array $params = [], ?string $connection = null): array
   {
      return self::query($sql, $params, $connection)->fetchAll();
   }

   public static function fetch(string $sql, array $params = [], ?string $connection = null): ?array
   {
      $row = self::query($sql, $params, $connection)->fetch();

      return $row === false ? null : $row;
   }

   public static function execute(string $sql, array $params = [], ?string $connection = null): int
   {
      return self::query($sql, $params, $connection)->rowCount();
   }

   public static function lastInsertId(?string $connection = null): string
   {
      return self::connection($connection)->lastInsertId();
   }

   public static function exists(string $sql, array $params = [], ?string $connection = null): bool
   {
      $stmt = self::query($sql, $params, $connection);

      return (bool) $stmt->fetchColumn();
   }

   public static function beginTransaction(?string $connection = null): bool
   {
      return self::connection($connection)->beginTransaction();
   }

   public static function commit(?string $connection = null): bool
   {
      return self::connection($connection)->commit();
   }

   public static function rollBack(?string $connection = null): bool
   {
      return self::connection($connection)->rollBack();
   }

   public static function inTransaction(?string $connection = null): bool
   {
      return self::connection($connection)->inTransaction();
   }

   public static function disconnect(?string $name = null): void
   {
      if ($name === null) {
         self::$connections = [];
         return;
      }

      unset(self::$connections[$name]);
   }

   public static function reconnect(?string $name = null): PDO
   {
      $name ??= self::getDefaultConnectionName();

      self::disconnect($name);

      return self::connection($name);
   }

   public static function driver(?string $connection = null): string
   {
      return (string) self::connection($connection)->getAttribute(PDO::ATTR_DRIVER_NAME);
   }

   private static function getDefaultConnectionName(): string
   {
      return (string) Config::get('database.default', 'default');
   }

   /**
    * @return array<string, mixed>
    */
   private static function getConnectionConfig(string $name): array
   {
      $config = Config::get("database.connections.{$name}");

      if (!is_array($config)) {
         throw new RuntimeException("Database connection [{$name}] is not configured.");
      }

      return $config;
   }

   /**
    * @param array<string, mixed> $config
    */
   private static function createPdo(array $config): PDO
   {
      $driver = strtolower((string) ($config['driver'] ?? ''));
      $host = (string) ($config['host'] ?? '127.0.0.1');
      $port = (int) ($config['port'] ?? 0);
      $database = (string) ($config['database'] ?? '');
      $username = (string) ($config['username'] ?? '');
      $password = (string) ($config['password'] ?? '');
      $charset = (string) ($config['charset'] ?? 'utf8mb4');

      $dsn = self::buildDsn($driver, $host, $port, $database, $charset);

      $options = [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false,
      ];

      if ($driver === 'pgsql') {
         $options[PDO::ATTR_EMULATE_PREPARES] = false;
      }

      try {
         return new PDO($dsn, $username, $password, $options);
      } catch (PDOException $e) {
         self::handleConnectionError($e);
      }
   }

   private static function buildDsn(
      string $driver,
      string $host,
      int $port,
      string $database,
      string $charset
   ): string {
      return match ($driver) {
         'mysql' => sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port > 0 ? $port : 3306,
            $database,
            $charset
         ),
         'pgsql' => sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;options=\'--client_encoding=%s\'',
            $host,
            $port > 0 ? $port : 5432,
            $database,
            $charset
         ),
         default => throw new RuntimeException("Unsupported database driver [{$driver}]."),
      };
   }

   private static function handleConnectionError(PDOException $e): never
   {
      http_response_code(500);

      echo Config::get('app.debug', false)
         ? 'Database connection error: ' . $e->getMessage()
         : 'Could not connect to the database.';

      exit;
   }
}