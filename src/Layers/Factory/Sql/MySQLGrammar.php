<?php

namespace Sienekib\Layers\Factory\Sql;

use Sienekib\Layers\Utils\Inflector;

/**
 * Class MySQLGrammar
 * @package Sienekib\Layers\Factory\Sql
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
     * Compile a SELECT SQL statement with specified fields and optional condition.
     *
     * @param string $table
     * @param string $fields
     * @param string $condition
     * @return string
     */
    public function compileSelectFields(string $table, string $fields, string $condition = ''): string
    {
        if (!empty($condition)) {
            return "SELECT {$fields} FROM {$table} WHERE {$condition}";
        } else {
            return "SELECT {$fields} FROM {$table}";
        }
    }

    /**
     * Compile an UPDATE SQL statement.
     *
     * @param string $table
     * @param array $fields
     * @param string $condition
     * @return string
     */
    public function compileUpdate(string $table, array $fields, string $condition): string
    {
        $setClause = implode(", ", array_map(fn($key) => "{$key} = ?", array_keys($fields)));
        return "UPDATE {$table} SET {$setClause} WHERE {$condition}";
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
     * Compile a TRUNCATE TABLE SQL statement.
     *
     * @param string $table
     * @return string
     */
    public function compileTruncate(string $table): string
    {
        return "TRUNCATE TABLE {$table}";
    }

    /**
     * Compile an EXISTS SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileExists(string $table, string $condition): string
    {
        return "SELECT EXISTS (SELECT 1 FROM {$table} WHERE {$condition})";
    }

    /**
     * Compile a COUNT SQL statement.
     *
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function compileCount(string $table, string $condition = ''): string
    {
        $whereClause = !empty($condition) ? " WHERE {$condition}" : "";
        return "SELECT COUNT(*) FROM {$table}{$whereClause}";
    }

    /**
     * Compile a RAW SQL statement.
     *
     * @param string $sql
     * @return string
     */
    public function compileRaw(string $sql): string
    {
        return $sql;
    }
}
