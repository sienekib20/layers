<?php

namespace Sienekib\Layers\Utils;

class Config
{
    protected static $driver;
    protected static $host;
    protected static $dbname;
    protected static $username;
    protected static $password;
    protected static $charset;
    protected static $persistent;

    public function __construct(array $config)
    {
        self::$driver = $config['driver'] ?? 'mysql';
        self::$host = $config['host'];
        self::$dbname = $config['database'];
        self::$username = $config['username'];
        self::$password = $config['password'];
        self::$charset = $config['charset'] ?? 'utf8mb4';
        self::$persistent = $config['persistent'] ?? false;
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    public static function getHost(): string
    {
        return self::$host;
    }

    public static function getDbname(): string
    {
        return self::$dbname;
    }

    public static function getUsername(): string
    {
        return self::$username;
    }

    public static function getPassword(): string
    {
        return self::$password;
    }

    public static function getCharset(): string
    {
        return self::$charset;
    }

    public static function isPersistent(): bool
    {
        return self::$persistent;
    }

    public static function getAllConfigurations() : array
    {
        return [
            'driver' => self::getDriver(),
            'host' => self::getHost(),
            'database' => self::getDbname(),
            'username' => self::getUsername(),
            'password' => self::getPassword(),
            'charset' => self::getCharset(),
            'persistent' => self::isPersistent()
        ];
    }
}
