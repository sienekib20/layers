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
     * Execute the sql non-ruled.
     *
     * @param string $sql
     * @param array $bindings
     * @return self
     */
    public static function raw(string $sql, array $bindings = []): array
    {
        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
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
     * @return array
     * @throws IgnitionException
     */
    public static function select(string $condition, array $bindings): array
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

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Select records from the table.
     *
     * @param string $condition
     * @return array
     * @throws IgnitionException
     */
    public static function selectFields(string $fields, string $condition = '', array $bindings = []): array
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
            $sql = self::$grammar->compileSelectFields(self::$table, $fields, $condition);
        } else {
            $sql = self::$grammar->compileSelectFields(self::$table, $fields);
        }

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Update a record in the table.
     *
     * @param array $fields
     * @param int $id
     * @return bool
     * @throws IgnitionException
     */
    public static function update(array $fields, int $id): bool
    {
        if (!self::$table) {
            try {
                throw new \Exception("Table not specified.");
            } catch (\Exception $e) {
                Ignition::make()->renderException($e);
            }
        }

        self::$rules->validateUpdateFields($fields);

        $sql = self::$grammar->compileUpdate(self::$table, $fields);

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $fields[] = $id;
        $result = $stmt->execute(array_values($fields));

        return $result;
    }

    /**
     * Delete records from the table.
     *
     * @param string $condition
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
}
