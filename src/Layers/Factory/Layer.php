<?php

namespace Sienekib\Layers\Factory;

use Sienekib\Layers\Connection\Connection;
use Sienekib\Layers\Factory\Sql\MySQLGrammar;
use Sienekib\Layers\Factory\Sql\Rules\MySQLRules;
use Spatie\Ignition\Exceptions\IgnitionException;
use PDO;
use Spatie\Ignition\Ignition;

/**
 * Class Layer
 * @package Sienekib\Layers\Factory
 *
 * This class acts as a factory for database operations, handling CRUD operations
 * with robust error checking and grammar support.
 */
class Layer
{
    protected static ?string $table = null;
    protected static MySQLGrammar $grammar;
    protected static MySQLRules $rules;

    public function __construct()
    {
        self::$grammar = new MySQLGrammar();
        self::$rules = new MySQLRules();
    }

    /**
     * Set the table for the subsequent operations.
     *
     * @param string $table
     * @return self
     */
    public static function table(string $table): self
    {
        self::$table = $table;
        return new self();
    }

    /**
     * Execute a raw SQL query with bindings.
     *
     * @param string $sql
     * @param array $bindings
     * @param int $fetchMode
     * @param int|null $limit
     * @return array
     * @throws \PDOException Em caso de erro na execução da consulta
     */
    public static function raw(string $sql, array $bindings = [], int $fetchMode = PDO::FETCH_ASSOC, ?int $limit = null): array
    {
        try {
            $stmt = Connection::getInstance()->getPdo()->prepare($sql);
            $stmt->execute($bindings);

            if ($limit !== null) {
                $stmt->setFetchMode($fetchMode);
                return $stmt->fetchAll();
            }

            return $stmt->fetchAll($fetchMode) ?? [];
        } catch (\PDOException $e) {
            // Aqui você pode tratar o erro conforme necessário, por exemplo, logar o erro ou lançar novamente
            throw new \PDOException("Error executing raw query: " . $e->getMessage());
        }
    }



    /**
     * Insert a record into the table.
     *
     * @param array $fields
     * @param bool $returnLastId
     * @return mixed
     * @throws IgnitionException
     */
    public static function insert(array $fields, bool $returnLastId = false)
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateInsertFields($fields);

        $sql = self::$grammar->compileInsert(self::$table, $fields);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $result = $stmt->execute(array_values($fields));

        if ($returnLastId) {
            return Connection::getInstance()->getPdo()->lastInsertId();
        }

        return $result;
    }

    /**
     * Select records from the table.
     *
     * @param string $condition
     * @param array $bindings
     * @param int $limit
     * @param bool $fetchFirst
     * @param string|null $orderBy
     * @param string $groupBy
     * @return array
     * @throws IgnitionException
     */
    public static function select(string $condition, array $bindings = [], int $limit = 0, bool $fetchFirst = false, string $orderBy = null, string $groupBy = ''): array
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateCondition($condition);

        $sql = self::$grammar->compileSelect(self::$table, $condition);

        if (!empty($groupBy)) {
            $sql .= " GROUP BY {$groupBy}";
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute($bindings);

        if ($fetchFirst) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a record in the table.
     *
     * @param array $fields
     * @param string $condition
     * @param array $bindings
     * @return bool
     * @throws IgnitionException
     */
    public static function update(array $fields, string $condition, array $bindings = []): bool
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateUpdateFields($fields);

        $sql = self::$grammar->compileUpdate(self::$table, $fields, $condition);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $mergedBindings = array_merge(array_values($fields), $bindings);
        $result = $stmt->execute($mergedBindings);

        return $result;
    }

    /**
     * Delete records from the table.
     *
     * @param string $condition
     * @param array $bindings
     * @return bool
     * @throws IgnitionException
     */
    public static function delete(string $condition, array $bindings = []): bool
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateCondition($condition);

        $sql = self::$grammar->compileDelete(self::$table, $condition);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        return $stmt->execute($bindings);
    }

    /**
     * Count records in the table.
     *
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public static function count(string $condition = '', array $bindings = []): int
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        if (!empty($condition)) {
            self::$rules->validateCondition($condition);
            $sql = self::$grammar->compileCount(self::$table, $condition);
        } else {
            $sql = self::$grammar->compileCount(self::$table);
        }

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute($bindings);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Truncate the table.
     *
     * @return bool
     * @throws IgnitionException
     */
    public static function truncate(): bool
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        $sql = self::$grammar->compileTruncate(self::$table);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Check if records exist in the table matching the condition.
     *
     * @param string $condition
     * @param array $bindings
     * @return bool
     * @throws IgnitionException
     */
    public static function exists(string $condition, array $bindings = []): bool
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateCondition($condition);

        $sql = self::$grammar->compileExists(self::$table, $condition);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute($bindings);

        return (bool) $stmt->fetchColumn();
    }
}
