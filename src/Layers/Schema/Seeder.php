<?php

namespace Sienekib\Layers\Schema;

use Sienekib\Layers\Connection\Connection;
use Sienekib\Layers\Facade\DB;
use Spatie\Ignition\Ignition;

class Seeder
{
    protected $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function run(array $seederRecords)
    {
        foreach ($seederRecords as $seeder) {
            if (! class_exists($seeder)) {
                try {
                    throw new \Exception("Seeder Records Not Found.");
                } catch (\Exception $e) {
                    Ignition::make()->renderException($e);
                }
                return false;
            }
            $seeder = new $seeder();
            $seeder->seed();
        }
    }
}
