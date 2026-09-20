<?php

namespace App\Prisma;

use InvalidArgumentException;

/**
 * ModelQuery
 *
 * Prisma-style query builder backed by PDO prepared statements. Every public
 * method treats the model's field map as the only source of column names, so
 * identifiers are never built from user input.
 */
class ModelQuery
{
    /** @var list<string> */
    private array $fields;

    /** @var array<string, true> */
    private array $fieldMap;

    /** @var array<string, true> */
    private array $dateFields;

    /** @var array<string, true> */
    private array $intFields;

    public function __construct(
        private readonly PrismaClient $client,
        private readonly string $table,
        array $fields,
        array $dateFields = [],
        array $intFields = ['id', 'mapOrder', 'userId', 'locationId'],
    ) {
        $this->fields = array_values(array_unique($fields));
        $this->fieldMap = array_fill_keys($this->fields, true);
        $this->dateFields = array_fill_keys($dateFields, true);
        $this->intFields = array_fill_keys($intFields, true);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    // ---------------------------------------------------------------------
    // Read operations
    // ---------------------------------------------------------------------

    /**
     * Fetch a single record by a unique constraint, e.g. ['email' => '...'].
     */
    public function findUnique(array $args): ?array
    {
        $where = $args['where'] ?? [];

        return $this->findFirst(['where' => $where, 'include' => $args['include'] ?? null]);
    }

    public function findFirst(array $args = []): ?array
    {
        $args['take'] = 1;
        $rows = $this->findMany($args);

        return $rows[0] ?? null;
    }

    public function findMany(array $args = []): array
    {
        $where = $args['where'] ?? [];
        $orderBy = $args['orderBy'] ?? [];
        $take = $args['take'] ?? null;
        $skip = $args['skip'] ?? 0;
        $select = $args['select'] ?? null;
        $include = $args['include'] ?? [];

        $this->assertWhere($where);

        [$whereSql, $params] = $this->compileWhere($where);

        $selectList = $this->buildSelectList($select);

        $sql = 'SELECT '.$selectList.' FROM '.$this->client->quoteIdent($this->table);
        if ($whereSql !== '') {
            $sql .= ' WHERE '.$whereSql;
        }

        $sql .= $this->buildOrderBy($orderBy);

        if ($take !== null) {
            if ((int) $skip > 0) {
                $sql .= ' OFFSET '.(int) $skip;
            }
            $sql .= ' LIMIT '.(int) $take;
        } elseif ((int) $skip > 0) {
            $sql .= ' OFFSET '.(int) $skip;
        }

        $rows = $this->execute($sql, $params);

        $rows = array_map(fn (array $row): array => $this->mapRow($row), array_values($rows));

        return $this->applyInclude($rows, $include, $select);
    }

    public function count(array $where = []): int
    {
        $this->assertWhere($where);
        [$whereSql, $params] = $this->compileWhere($where);

        $sql = 'SELECT COUNT(*) AS c FROM '.$this->client->quoteIdent($this->table);
        if ($whereSql !== '') {
            $sql .= ' WHERE '.$whereSql;
        }

        $row = $this->execute($sql, $params);

        return (int) ($row[0]['c'] ?? 0);
    }

    // ---------------------------------------------------------------------
    // Write operations
    // ---------------------------------------------------------------------

    /**
     * Insert one record and return it with database defaults applied.
     */
    public function create(array $args): array
    {
        $data = $this->assertData($args['data'] ?? [], allowDefaults: true);
        $data = $this->normaliseDataForWrite($data);

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = 'INSERT INTO '.$this->client->quoteIdent($this->table)
            .' ('.implode(', ', array_map(fn ($c) => $this->client->quoteIdent($c), $columns)).')'
            .' VALUES ('.implode(', ', $placeholders).')'
            .' RETURNING *';

        $rows = $this->execute($sql, array_values($data));

        return $this->mapRow($rows[0]);
    }

    /**
     * Insert many records in a single statement.
     *
     * @return int Number of inserted rows.
     */
    public function createMany(array $args): int
    {
        $rows = $args['data'] ?? [];
        if ($rows === []) {
            return 0;
        }

        $normalised = [];
        foreach ($rows as $row) {
            $normalised[] = $this->normaliseDataForWrite($this->assertData($row, allowDefaults: true));
        }

        // Union of all keys so rows with different fields still work.
        $columns = [];
        foreach ($normalised as $row) {
            foreach (array_keys($row) as $key) {
                $columns[$key] = true;
            }
        }
        $columns = array_keys($columns);

        $inserts = [];
        $params = [];
        foreach ($normalised as $row) {
            $rowParams = [];
            foreach ($columns as $column) {
                $rowParams[] = $row[$column] ?? null;
            }
            $inserts[] = '('.implode(', ', array_fill(0, count($columns), '?')).')';
            $params = array_merge($params, $rowParams);
        }

        $sql = 'INSERT INTO '.$this->client->quoteIdent($this->table)
            .' ('.implode(', ', array_map(fn ($c) => $this->client->quoteIdent($c), $columns)).')'
            .' VALUES '.implode(', ', $inserts);

        return $this->execute($sql, $params);
    }

    /**
     * Update a single record matched by unique fields and return the result.
     */
    public function update(array $args): ?array
    {
        $this->assertWhere($args['where'] ?? []);
        $data = $this->assertData($args['data'] ?? []);
        $data = $this->normaliseDataForWrite($data);

        if ($data === []) {
            return $this->findFirst($args);
        }

        $this->touchUpdatedAt($data);

        [$whereSql, $params] = $this->compileWhere($args['where']);

        $setSql = implode(', ', array_map(
            fn ($column) => $this->client->quoteIdent($column).' = ?',
            array_keys($data)
        ));

        $sql = 'UPDATE '.$this->client->quoteIdent($this->table)
            .' SET '.$setSql
            .(($whereSql !== '') ? ' WHERE '.$whereSql : '')
            .' RETURNING *';

        $rows = $this->execute($sql, array_merge(array_values($data), $params));

        return $rows === [] ? null : $this->mapRow($rows[0]);
    }

    /**
     * Update every matching record.
     *
     * @return int Number of updated rows.
     */
    public function updateMany(array $args): int
    {
        $where = $args['where'] ?? [];
        $this->assertWhere($where);
        $data = $this->normaliseDataForWrite($this->assertData($args['data'] ?? []));

        $this->touchUpdatedAt($data);

        if ($data === []) {
            return 0;
        }

        [$whereSql, $params] = $this->compileWhere($where);

        $setSql = implode(', ', array_map(
            fn ($column) => $this->client->quoteIdent($column).' = ?',
            array_keys($data)
        ));

        // RETURNING is not needed here; the affected-row count is what matters.
        $sql = 'UPDATE '.$this->client->quoteIdent($this->table).' SET '.$setSql
            .(($whereSql !== '') ? ' WHERE '.$whereSql : '');

        return $this->execute($sql, array_merge(array_values($data), $params));
    }

    /**
     * Delete every matching record.
     *
     * @return int Number of deleted rows.
     */
    public function deleteMany(array $where = []): int
    {
        $this->assertWhere($where);
        [$whereSql, $params] = $this->compileWhere($where);

        $sql = 'DELETE FROM '.$this->client->quoteIdent($this->table)
            .(($whereSql !== '') ? ' WHERE '.$whereSql : '');

        return $this->execute($sql, $params);
    }

    // ---------------------------------------------------------------------
    // Where clause builder
    // ---------------------------------------------------------------------

    /**
     * @return array{0: string, 1: list<mixed>}  [sql, params]
     */
    private function compileWhere(array $where): array
    {
        $params = [];

        $sql = $this->compileWhereGroup($where, $params);

        return [$sql, $params];
    }

    /**
     * @param  list<mixed>  $params
     */
    private function compileWhereGroup(array $clauses, array &$params): string
    {
        $parts = [];

        foreach ($clauses as $key => $value) {
            switch ($key) {
                case 'AND':
                    $sub = array_map(
                        fn ($branch) => '('.$this->compileWhereGroup($branch, $params).')',
                        array_values((array) $value)
                    );
                    $parts[] = implode(' AND ', $sub);
                    break;

                case 'OR':
                    $sub = array_map(
                        fn ($branch) => '('.$this->compileWhereGroup($branch, $params).')',
                        array_values((array) $value)
                    );
                    $parts[] = '('.(implode(' OR ', $sub)).')';
                    break;

                case 'NOT':
                    $parts[] = 'NOT ('.$this->compileWhereGroup((array) $value, $params).')';
                    break;

                default:
                    $parts[] = $this->compileField($key, $value, $params);
                    break;
            }
        }

        return implode(' AND ', $parts);
    }

    /**
     * Compile a single field condition (shorthand value or operator object).
     *
     * @param  list<mixed>  $params
     */
    private function compileField(string $field, mixed $value, array &$params): string
    {
        $this->assertField($field);

        $column = $this->client->quoteIdent($field);

        if (is_array($value)) {
            $parts = [];
            $mode = null;

            foreach ($value as $operator => $operand) {
                if ($operator === 'mode') {
                    $mode = $operand;
                    continue;
                }

                $parts[] = $this->compileOperator($column, $field, $operator, $operand, $params, $mode);
            }

            return $parts === [] ? 'TRUE' : implode(' AND ', $parts);
        }

        return $this->compileOperator($column, $field, 'equals', $value, $params, null);
    }

    /**
     * @param  list<mixed>  $params
     */
    private function compileOperator(
        string $column,
        string $field,
        string $operator,
        mixed $operand,
        array &$params,
        ?string $mode,
    ): string {
        $insensitive = $mode === 'insensitive';
        $asText = fn ($v): mixed => isset($this->dateFields[$field]) && $v !== null
            ? $this->client->toDatabaseDateTime($v)
            : $v;

        return match ($operator) {
            'equals' => $operand === null
                ? $column.' IS NULL'
                : $this->column($column, $insensitive).' = '.$this->bind($asText($operand), $params),
            'not' => $operand === null
                ? $column.' IS NOT NULL'
                : $this->column($column, $insensitive).' <> '.$this->bind($asText($operand), $params),
            'in' => $this->inClause($column, $operand, $params, $not = false),
            'notIn' => $this->inClause($column, $operand, $params, $not = true),
            'lt' => $column.' < '.$this->bind($asText($operand), $params),
            'lte' => $column.' <= '.$this->bind($asText($operand), $params),
            'gt' => $column.' > '.$this->bind($asText($operand), $params),
            'gte' => $column.' >= '.$this->bind($asText($operand), $params),
            'contains' => $this->column($column, $insensitive).' LIKE '.$this->bind('%'.$this->escapeLike($this->asString($asText($operand))).'%', $params).' ESCAPE \'\\\'',
            'startsWith' => $this->column($column, $insensitive).' LIKE '.$this->bind($this->escapeLike($this->asString($asText($operand))).'%', $params).' ESCAPE \'\\\'',
            'endsWith' => $this->column($column, $insensitive).' LIKE '.$this->bind('%'.$this->escapeLike($this->asString($asText($operand))), $params).' ESCAPE \'\\\'',
            default => throw new InvalidArgumentException("Unsupported where operator: {$operator}"),
        };
    }

    private function column(string $column, bool $insensitive): string
    {
        return $insensitive ? 'LOWER('.$column.')' : $column;
    }

    /**
     * @param  list<mixed>  $params
     */
    private function inClause(string $column, mixed $values, array &$params, bool $not): string
    {
        $values = array_values((array) $values);
        if ($values === []) {
            return $not ? 'TRUE' : 'FALSE';
        }

        $placeholders = array_map(fn ($value) => $this->bind($value, $params), $values);

        return $column.($not ? ' NOT IN (' : ' IN (').implode(', ', $placeholders).')';
    }

    private function bind(mixed $value, array &$params): string
    {
        $params[] = $this->prepareValue($value);

        return '?';
    }

    private function prepareValue(mixed $value): mixed
    {
        if ($value instanceof \backedEnum) {
            return $value->value;
        }

        return $value;
    }

    private function asString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function buildSelectList(?array $select): string
    {
        if ($select === null || $select === []) {
            return '*';
        }

        $fields = [];
        foreach ($select as $field) {
            $this->assertField($field);
            $fields[] = $this->client->quoteIdent($field);
        }

        return implode(', ', $fields);
    }

    private function buildOrderBy(mixed $orderBy): string
    {
        $order = is_array($orderBy) && isset($orderBy[0]) && is_array($orderBy[0])
            ? $orderBy
            : ($orderBy === [] ? [] : [$orderBy]);

        if ($order === []) {
            return '';
        }

        $parts = [];
        foreach ($order as $entry) {
            foreach ((array) $entry as $field => $direction) {
                $this->assertField($field);
                $dir = strtolower((string) $direction) === 'desc' ? 'DESC' : 'ASC';
                $parts[] = $this->client->quoteIdent($field).' '.$dir;
            }
        }

        return ' ORDER BY '.implode(', ', $parts);
    }

    /**
     * Apply "include" relations to fetched rows (currently: SavedLocation->location).
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function applyInclude(array $rows, mixed $include, ?array $select): array
    {
        if ($include === null || $include === [] || $rows === []) {
            return $rows;
        }

        $requireLocation = ($include === true) || (is_array($include) && isset($include['location']));

        if (! $requireLocation || $this->table !== 'SavedLocation') {
            return $rows;
        }

        $locationIds = array_values(array_unique(array_map(
            fn ($row): int => (int) $row['locationId'],
            $rows
        )));

        $locations = $this->client->location->findMany([
            'where' => ['id' => ['in' => $locationIds]],
        ]);

        $byId = [];
        foreach ($locations as $location) {
            $byId[$location['id']] = $location;
        }

        foreach ($rows as $index => $row) {
            $rows[$index]['location'] = $byId[(int) $row['locationId']] ?? null;
        }

        return $rows;
    }

    /**
     * Cast raw database rows into typed, API-safe arrays.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function mapRow(array $row): array
    {
        $mapped = [];

        foreach ($row as $key => $value) {
            if (! isset($this->fieldMap[$key])) {
                continue;
            }

            if (isset($this->dateFields[$key])) {
                $mapped[$key] = $this->client->fromDatabaseDateTime($value);
            } elseif (isset($this->intFields[$key])) {
                $mapped[$key] = $value === null ? null : (int) $value;
            } elseif ($value === null) {
                $mapped[$key] = null;
            } else {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    private function assertWhere(array $where): void
    {
        foreach (array_keys($where) as $key) {
            if (is_string($key) && ! in_array($key, ['AND', 'OR', 'NOT'], true)) {
                $this->assertField($key);
            }
        }
    }

    /**
     * @throws InvalidArgumentException when a field is not part of this model.
     */
    private function assertField(string $field): void
    {
        if (! isset($this->fieldMap[$field])) {
            throw new InvalidArgumentException("Unknown field \"{$field}\" on \"{$this->table}\".");
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function assertData(array $data, bool $allowDefaults = false): array
    {
        $clean = [];

        foreach ($data as $field => $value) {
            $this->assertField($field);

            if ($field === 'id' && ! $allowDefaults) {
                throw new InvalidArgumentException('The "id" field cannot be written explicitly.');
            }

            $clean[$field] = $value;
        }

        return $clean;
    }

    /**
     * Convert DateTime values into DB-ready strings.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normaliseDataForWrite(array $data): array
    {
        foreach ($data as $field => $value) {
            if (isset($this->dateFields[$field]) && $value !== null) {
                $data[$field] = $this->client->toDatabaseDateTime($value);
            }
        }

        return $data;
    }

    /**
     * Keep "updatedAt" fresh on updates, mirroring Prisma's @updatedAt intent.
     *
     * @param  array<string, mixed>  $data
     */
    private function touchUpdatedAt(array &$data): void
    {
        if (isset($this->fieldMap['updatedAt']) && ! array_key_exists('updatedAt', $data)) {
            $data['updatedAt'] = $this->client->toDatabaseDateTime(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        }
    }

    /**
     * Execute a prepared statement; returns affected-row count for DML without
     * RETURNING, otherwise the returned rows.
     */
    private function execute(string $sql, array $params): int|array
    {
        // TEMP DEBUG: log the exact SQL + params.
        file_put_contents(
            __DIR__.'/sql-debug.log',
            date('H:i:s').' '.$sql."\n    params: ".json_encode($params)."\n",
            FILE_APPEND
        );

        $statement = $this->client->connection()->prepare($sql);
        $statement->execute($params);

        if (stripos(ltrim($sql), 'SELECT') === 0 || stripos($sql, 'RETURNING') !== false) {
            return $statement->fetchAll() ?: [];
        }

        return $statement->rowCount();
    }
}