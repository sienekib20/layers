<?php

namespace Sienekib\Layers\Connection;

use Sienekib\Layers\Utils\Config;
use PDO;
use Spatie\Ignition\Ignition;

/**
 * Class Connection
 * @package Sienekib\Layers\Connection
 *
 * This class handles the database connection using PDO.
 */
class Connection
{
    protected static $instance = null;
    protected $pdo;

    /**
     * Connection constructor.
     *
     * @throws IgnitionException
     */
    private function __construct()
    {
        $config = Config::getAllConfigurations();

        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($config['persistent']) {
            $options[PDO::ATTR_PERSISTENT] = true;
        }

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (\PDOException $e) {
            Ignition::make()->renderException($e);
            //throw new IgnitionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the singleton instance of the Connection.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * Get the PDO instance.
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Rollback a transaction.
     */
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }
}
