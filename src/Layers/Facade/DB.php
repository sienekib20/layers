<?php

namespace Sienekib\Layers\Facade;

use Sienekib\Layers\Factory\Layer;
use Spatie\Ignition\Exceptions\IgnitionException;

/**
 * Class DB
 * @package Sienekib\Layers\Facade
 *
 * This class provides a simple and convenient interface for database operations.
 */
class DB
{
    protected static ?string $table = null;

    /**
     * Execute a select query.
     *
     * @param string $table
     * @return DB
     */
    public static function table(string $table)
    {
        self::$table = $table;

        return new self();
    }

    /**
     * Execute a select query.
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     * @throws IgnitionException
     */
    public static function raw(string $sql, array $bindings = []): array
    {
        return Layer::raw($sql, $bindings);
    }

    /**
     * Execute a select query.
     *
     * @param string $table
     * @param string $condition
     * @param array $bindings
     * @return array
     * @throws IgnitionException
     */
    public static function select(string $condition, array $bindings = []): array
    {
        return Layer::table(static::$table)->select($condition, $bindings);
    }

    /**
     * Execute a select query.
     *
     * @param string $table
     * @param string $condition
     * @param array $bindings
     * @return array
     * @throws IgnitionException
     */
    public static function fields(string $fields = '*', string $condition = '', array $bindings = []): array
    {
        return Layer::table(static::$table)->selectFields($fields, $condition, $bindings);
    }

    /**
     * Execute an insert query.
     *
     * @param string $table
     * @param array $fields
     * @return bool
     * @throws IgnitionException
     */
    public static function insert(array $fields): bool
    {
        return Layer::table(static::$table)->insert($fields);
    }

    /**
     * Execute an insert query.
     *
     * @param string $table
     * @param array $fields
     * @return bool
     * @throws IgnitionException
     */
    public static function records(string $table, array $fields): bool
    {
        foreach ($fields as $field) {
            Layer::table($table)->insert($field);
        }
        return true;
    }

    /**
     * Execute an update query.
     *
     * @param string $table
     * @param array $fields
     * @param int $id
     * @return bool
     * @throws IgnitionException
     */
    public static function update(array $fields, int $id): bool
    {
        return Layer::table(static::$table)->update($fields, $id);
    }

    /**
     * Execute a delete query.
     *
     * @param string $table
     * @param string $condition
     * @return bool
     * @throws IgnitionException
     */
    public static function delete(string $condition): bool
    {
        return Layer::table(static::$table)->delete($condition);
    }
}
