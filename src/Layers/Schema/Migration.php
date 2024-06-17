<?php

namespace Sienekib\Layers\Schema;

use Sienekib\Layers\Connection\Connection;

class Migration
{
    protected $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function run()
    {
        // Implementar nas subclasses
    }

    protected function createTable(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $sql = $blueprint->toSql();
        $this->connection->getPdo()->exec($sql);
    }

    protected function dropTable(string $table)
    {
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->connection->getPdo()->exec($sql);
    }
}
