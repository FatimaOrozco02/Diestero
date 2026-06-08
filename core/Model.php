<?php

declare(strict_types=1);

namespace Core;

use InvalidArgumentException;
use ReflectionClass;

abstract class Model
{
   protected string $table = '';
   protected string $primaryKey = 'id';

   /**
    * Null = use default database connection.
    */
   protected ?string $connection = null;

   /**
    * @var array<int, string>
    */
   protected array $fillable = [];

   /**
    * Obtiene el nombre de la tabla.
    */
   protected function table(): string
   {
      if ($this->table !== '') {
         return $this->table;
      }

      $class = (new ReflectionClass($this))->getShortName();
      $snake = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

      return $snake . 's';
   }

   /**
    * Obtiene la llave primaria.
    */
   protected function pk(): string
   {
      return $this->primaryKey;
   }

   /**
    * Obtiene el nombre de la conexión del modelo.
    */
   protected function connectionName(): ?string
   {
      return $this->connection;
   }

   /**
    * SELECT, devuelve múltiples filas.
    */
   public function get(string $sql, array $params = []): array
   {
      return Database::fetchAll($sql, $params, $this->connectionName());
   }

   /**
    * SELECT, para catálogos.
    */
   public function getActiveRecords(): array
   {
      $sql = sprintf(
         'SELECT %s, name FROM %s WHERE is_active AND deleted_at IS NULL ORDER BY name ASC',
         $this->pk(),
         $this->table()
      );

      return Database::fetchAll($sql, [], $this->connectionName());
   }
   
   /**
    * SELECT, para catálogos con deleted_at.
    */
   public function getSoftActiveRecords(): array
   {
      $sql = sprintf(
         'SELECT %s, %s FROM %s WHERE %s AND %s IS NULL ORDER BY %s ASC',
         $this->pk(),
         $this->sanitizeIdentifier('name'),
         $this->table(),
         $this->sanitizeIdentifier('is_active'),
         $this->sanitizeIdentifier('deleted_at'),
         $this->sanitizeIdentifier('name')
      );

      return Database::fetchAll($sql, [], $this->connectionName());
   }

   public function getById(int|string $id): ?array {
      $sql = sprintf(
         "SELECT * FROM %s WHERE is_active = TRUE AND %s = ?",
         $this->table(),
         $this->pk()
      );

      return Database::fetchAll($sql, [$id], $this->connectionName());
   }

   /**
    * SELECT, devuelve 1 fila.
    */
   public function first(string $sql, array $params = []): ?array
   {
      return Database::fetch($sql, $params, $this->connectionName());
   }

   /**
    * SELECT de un registro por PK.
    */
   public function find(int|string $id): ?array
   {
      $sql = sprintf(
         'SELECT * FROM %s WHERE %s = ? LIMIT 1',
         $this->table(),
         $this->pk()
      );

      return Database::fetch($sql, [$id], $this->connectionName());
   }

   /** SELECT general de un registro */
   public function findGeneral(array $fields = ['*'], array $where = [], ?string $orderBy = null): ?array
   {
      $select = $this->buildSelectFields($fields);
      $sql = "SELECT {$select} FROM {$this->table()}";
      $params = [];

      if (!empty($where)) {
         $whereClause = $this->buildWhere($where);
         $sql .= " WHERE {$whereClause['sql']}";
         $params = $whereClause['params'];
      }

      if ($orderBy !== null && trim($orderBy) !== '') {
         $sql .= ' ORDER BY ' . $this->sanitizeOrderBy($orderBy);
      }

      return Database::fetch($sql, $params, $this->connectionName());
   }

   /**
    * SELECT EXISTS de la tabla principal, retorna verdadero/falso.
    */
   public function exists(array $where): bool
   {
      $whereClause = $this->buildWhere($where);

      $sql = sprintf(
         'SELECT EXISTS (SELECT 1 FROM %s WHERE %s)',
         $this->table(),
         $whereClause['sql']
      );

      return Database::exists($sql, $whereClause['params'], $this->connectionName());
   }

   /**
    * SELECT EXISTS con SQL explícito.
    */
   protected function existsBySql(string $sql, array $params = []): bool
   {
      return Database::exists($sql, $params, $this->connectionName());
   }

   /**
    * SELECT, cuenta registros de la tabla.
    */
   public function count(array $where = []): int
   {
      $params = [];
      $sql = sprintf('SELECT COUNT(*) FROM %s', $this->table());

      if (!empty($where)) {
         $whereClause = $this->buildWhere($where);
         $sql .= " WHERE {$whereClause['sql']}";
         $params = $whereClause['params'];
      }

      return (int) Database::query($sql, $params, $this->connectionName())->fetchColumn();
   }

   /**
    * SELECT, obtiene un solo valor escalar (COUNT, MAX, etc.).
    */
   public function value(string $sql, array $params = []): mixed
   {
      return Database::query($sql, $params, $this->connectionName())->fetchColumn();
   }

   /**
    * INSERT, retorna el id insertado.
    */
   public function create(array $data): string
   {
      $data = $this->filterFillable($data);

      if (empty($data)) {
         throw new InvalidArgumentException('No hay datos por llenar.');
      }

      $columns = array_keys($data);
      $columnList = implode(', ', array_map(
         fn(string $column): string => $this->sanitizeIdentifier($column),
         $columns
      ));
      $placeholders = implode(', ', array_fill(0, count($columns), '?'));

      $driver = Database::driver($this->connectionName());

      if ($driver === 'pgsql') {
         $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING %s',
            $this->table(),
            $columnList,
            $placeholders,
            $this->sanitizeIdentifier($this->pk())
         );

         $id = Database::query($sql, array_values($data), $this->connectionName())->fetchColumn();

         if ($id === false) {
            throw new \RuntimeException('No fue posible obtener el ID insertado.');
         }

         return (string) $id;
      }

      $sql = sprintf(
         'INSERT INTO %s (%s) VALUES (%s)',
         $this->table(),
         $columnList,
         $placeholders
      );

      Database::execute($sql, array_values($data), $this->connectionName());

      return Database::lastInsertId($this->connectionName());
   }

   /**
    * INSERT masivo, retorna número de filas insertadas.
    */
   public function createMassive(array $rows): int
   {
      if (empty($rows)) {
         throw new InvalidArgumentException('No hay datos para insertar.');
      }

      $filteredRows = [];

      foreach ($rows as $row) {
         $filtered = $this->filterFillable($row);

         if (!empty($filtered)) {
            $filteredRows[] = $filtered;
         }
      }

      if (empty($filteredRows)) {
         throw new InvalidArgumentException('Todos los registros están vacíos o no contienen campos permitidos.');
      }

      $columns = array_keys($filteredRows[0]);

      foreach ($filteredRows as $row) {
         if (array_keys($row) !== $columns) {
            throw new InvalidArgumentException('Todos los registros deben tener las mismas columnas.');
         }
      }

      $columnList = implode(', ', array_map(fn(string $column): string => $this->sanitizeIdentifier($column), $columns));
      $rowPlaceholder = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
      $placeholders = implode(', ', array_fill(0, count($filteredRows), $rowPlaceholder));

      $sql = sprintf(
         'INSERT INTO %s (%s) VALUES %s',
         $this->table(),
         $columnList,
         $placeholders
      );

      $values = [];

      foreach ($filteredRows as $row) {
         foreach ($columns as $column) {
            $values[] = $row[$column];
         }
      }

      return Database::execute($sql, $values, $this->connectionName());
   }

   /**
    * UPDATE de un registro por su ID.
    */
   public function update(int|string $id, array $data): int
   {
      return $this->updateWhere([$this->pk() => $id], $data);
   }

   /**
    * UPDATE masivo por condiciones.
    */
   public function updateWhere(array $where, array $data): int
   {
      $data = $this->filterFillable($data);

      if (empty($data)) {
         throw new InvalidArgumentException('No data provided for updateWhere().');
      }

      $set = implode(
         ', ',
         array_map(
            fn(string $col): string => $this->sanitizeIdentifier($col) . ' = ?',
            array_keys($data)
         )
      );

      $whereClause = $this->buildWhere($where);

      $sql = sprintf(
         'UPDATE %s SET %s WHERE %s',
         $this->table(),
         $set,
         $whereClause['sql']
      );

      $params = array_values($data);
      $params = array_merge($params, $whereClause['params']);

      return Database::execute($sql, $params, $this->connectionName());
   }

   /**
    * DELETE de un registro por su ID.
    */
   public function delete(int|string $id): int
   {
      return $this->deleteWhere([$this->pk() => $id]);
   }

   /**
    * DELETE Lógico de un registro por su ID.
    */
   public function softDelete(int|string $id): int
   {
      return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
   }

   /**
    * DELETE masivo por condiciones.
    */
   public function deleteWhere(array $where): int
   {
      $whereClause = $this->buildWhere($where);

      $sql = sprintf(
         'DELETE FROM %s WHERE %s',
         $this->table(),
         $whereClause['sql']
      );

      return Database::execute($sql, $whereClause['params'], $this->connectionName());
   }

   /**
    * Filtra los campos fillable.
    */
   protected function filterFillable(array $data): array
   {
      if (empty($this->fillable)) {
         return $data;
      }

      return array_intersect_key($data, array_flip($this->fillable));
   }

   public function getGeneral(array $fields = ['*'], array $where = [], ?string $orderBy = null): array
   {
      $select = $this->buildSelectFields($fields);
      $sql = "SELECT {$select} FROM {$this->table()}";
      $params = [];

      if (!empty($where)) {
         $whereClause = $this->buildWhere($where);
         $sql .= " WHERE {$whereClause['sql']}";
         $params = $whereClause['params'];
      }

      if ($orderBy !== null && trim($orderBy) !== '') {
         $sql .= ' ORDER BY ' . $this->sanitizeOrderBy($orderBy);
      }

      return Database::fetchAll($sql, $params, $this->connectionName());
   }

   protected function buildSelectFields(array $fields): string
   {
      if (empty($fields)) {
         return '*';
      }

      if (count($fields) === 1 && $fields[0] === '*') {
         return '*';
      }

      $sanitized = array_map(
         fn(string $field): string => $this->sanitizeIdentifier($field),
         $fields
      );

      return implode(', ', $sanitized);
   }

   protected function sanitizeIdentifier(string $identifier): string
   {
      $identifier = trim($identifier);

      if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
         throw new InvalidArgumentException("Identificador inválido: {$identifier}");
      }

      return $identifier;
   }

   protected function sanitizeOrderBy(string $orderBy): string
   {
      $parts = preg_split('/\s+/', trim($orderBy));

      $column = $parts[0] ?? '';
      $direction = strtoupper($parts[1] ?? 'ASC');

      $column = $this->sanitizeIdentifier($column);

      if (!in_array($direction, ['ASC', 'DESC'], true)) {
         throw new InvalidArgumentException("Dirección ORDER BY inválida: {$direction}");
      }

      return "{$column} {$direction}";
   }

   /**
    * @return array{sql: string, params: array<int, mixed>}
    */
   protected function buildWhere(array $where): array
   {
      if (empty($where)) {
         throw new InvalidArgumentException('Las condiciones del WHERE deben ser establecidas.');
      }

      $params = [];
      $sql = $this->compileWhere($where, $params);

      if ($sql === '') {
         throw new InvalidArgumentException('No se pudo construir la cláusula WHERE.');
      }

      return [
         'sql' => $sql,
         'params' => $params,
      ];
   }

   protected function compileWhere(array $filters, array &$params = [], string $defaultGlue = 'AND'): string
   {
      $parts = [];

      if ($this->isSimpleWhereArray($filters)) {
         foreach ($filters as $column => $value) {
            $column = $this->sanitizeIdentifier((string) $column);

            if (is_array($value)) {
               if (empty($value)) {
                  continue;
               }

               $placeholders = implode(', ', array_fill(0, count($value), '?'));
               $parts[] = "{$column} IN ({$placeholders})";
               $params = array_merge($params, array_values($value));
            } elseif ($value === null) {
               $parts[] = "{$column} IS NULL";
            } else {
               $parts[] = "{$column} = ?";
               $params[] = $value;
            }
         }

         return implode(" {$defaultGlue} ", $parts);
      }

      foreach ($filters as $key => $filter) {
         if ($key === 'and' && is_array($filter)) {
            $nested = $this->compileWhere($filter, $params, 'AND');

            if ($nested !== '') {
               $parts[] = "({$nested})";
            }

            continue;
         }

         if ($key === 'or' && is_array($filter)) {
            $nested = $this->compileWhere($filter, $params, 'OR');

            if ($nested !== '') {
               $parts[] = "({$nested})";
            }

            continue;
         }

         if (!is_array($filter)) {
            continue;
         }

         $field = null;
         $operator = '=';
         $value = null;

         if (isset($filter['field'])) {
            $field = $filter['field'];
            $operator = strtoupper(trim((string) ($filter['operator'] ?? '=')));
            $value = $filter['value'] ?? null;
         } else {
            $count = count($filter);

            if ($count === 2) {
               $field = $filter[0] ?? null;

               if (is_string($filter[1]) && in_array(strtoupper($filter[1]), ['IS NULL', 'IS NOT NULL'], true)) {
                  $operator = strtoupper($filter[1]);
               } else {
                  $operator = '=';
                  $value = $filter[1];
               }
            } elseif ($count === 3) {
               $field = $filter[0] ?? null;
               $operator = strtoupper(trim((string) ($filter[1] ?? '=')));
               $value = $filter[2] ?? null;
            }
         }

         if (!$field) {
            continue;
         }

         $field = $this->sanitizeIdentifier((string) $field);

         if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
            $parts[] = "{$field} {$operator}";
            continue;
         }

         if ($operator === 'IN') {
            if (!is_array($value) || empty($value)) {
               continue;
            }

            $placeholders = implode(', ', array_fill(0, count($value), '?'));
            $parts[] = "{$field} IN ({$placeholders})";
            $params = array_merge($params, array_values($value));
            continue;
         }

         if ($operator === 'BETWEEN') {
            if (!is_array($value) || count($value) !== 2) {
               continue;
            }

            $parts[] = "{$field} BETWEEN ? AND ?";
            $params[] = $value[0];
            $params[] = $value[1];
            continue;
         }

         if (in_array($operator, ['=', '!=', '<>', '>', '<', '>=', '<=', 'LIKE'], true)) {
            $parts[] = "{$field} {$operator} ?";
            $params[] = $value;
         }
      }

      return implode(" {$defaultGlue} ", $parts);
   }

   protected function isSimpleWhereArray(array $filters): bool
   {
      foreach ($filters as $key => $value) {
         if (!is_string($key)) {
            return false;
         }

         if (in_array($key, ['and', 'or'], true)) {
            return false;
         }

         if (is_array($value) && isset($value['field'])) {
            return false;
         }
      }

      return true;
   }
}
