<?php

namespace Sienekib\Layers\Models;

use Sienekib\Layers\Factory\Layer;
use Sienekib\Layers\Utils\Inflector;

/**
 * Class BaseModel
 * @package Sienekib\Layers\Models
 *
 * This class serves as a base model for other models to extend.
 */
class BaseModel
{
    protected static $table;

    /**
     * Get all records from the table.
     *
     * @return array
     */
    public static function all(): array
    {
        return Layer::table(static::$table)->select("1", []);
    }

    /**
     * Find a record by ID.
     *
     * @param int $id
     * @return array
     */
    public static function find(int $id): array
    {
        $tableIdFieldName = Inflector::singularize(static::$table, 'pt') . '_id';
        $results = Layer::table(static::$table)->select("{$tableIdFieldName} = ?", [$id]);
        return $results[0] ?? [];
    }

    /**
     * Create a new record in the table.
     *
     * @param array $attributes
     * @return bool
     */
    public static function create(array $attributes, bool $returnLastId = false): bool
    {
        return Layer::table(static::$table)->insert($attributes, $returnLastId);
    }

    /**
     * Update a record by ID.
     *
     * @param array $attributes
     * @param int $id
     * @return bool
     */
    public static function update(array $attributes, int $id): bool
    {
        return Layer::table(static::$table)->update($attributes, $id);
    }

    /**
     * Delete a record by ID.
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $tableIdFieldName = Inflector::singularize(static::$table, 'pt') . '_id';
        return Layer::table(static::$table)->delete("{$tableIdFieldName} = ?", [$id]);
    }
}
