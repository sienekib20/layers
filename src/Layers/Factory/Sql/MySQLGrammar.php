<?php

namespace Sienekib\Layers\Factory\Sql;

use Sienekib\Layers\Utils\Inflector;

/**
 * Class MySQLGrammar
 * @package Sienekib\Layers\Grammar
 *
 * This class is responsible for compiling SQL statements.
 */
class MySQLGrammar
{
    /**
     * Compile an INSERT SQL statement.
     *
     * @param string $table
     * @param array $fields
     * @return string
     */
    public function compileInsert(string $table, array $fields): string
    {
        $columns = implode(", ", array_keys($fields));
        $placeholders = implode(", ", array_fill(0, count($fields), '?'));
        return "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    }

    /**
     * Compile a SELECT SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileSelect(string $table, string $condition): string
    {
        return "SELECT * FROM {$table} WHERE {$condition}";
    }

    /**
     * Compile a SELECT SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileSelectFields(string $table, string $fields, string $condition = ''): string
    {
        return !empty($condition) ? "SELECT {$fields} FROM {$table} WHERE {$condition}" : "SELECT {$fields} FROM {$table}";
    }

    /**
     * Compile an UPDATE SQL statement.
     *
     * @param string $table
     * @param array $fields
     * @return string
     */
    public function compileUpdate(string $table, array $fields): string
    {
        $setClause = implode(", ", array_map(fn($key) => "{$key} = ?", array_keys($fields)));
        $tableIdFieldName = Inflector::singularize($table, 'pt') . '_id';
        return "UPDATE {$table} SET {$setClause} WHERE {$tableIdFieldName} = ?";
    }

    /**
     * Compile a DELETE SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileDelete(string $table, string $condition): string
    {
        return "DELETE FROM {$table} WHERE {$condition}";
    }

    /**
     * Compile a RAW SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileRaw(string $sql): string
    {
        return $sql;
    }
}
