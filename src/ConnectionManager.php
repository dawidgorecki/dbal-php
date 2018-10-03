<?php

namespace Reven\DBAL;

use PDO;
use Reven\DBAL\Configuration\DBConfig;
use Reven\DBAL\Exceptions\DBALException;

/**
 * Class ConnectionManager
 * @package Reven\DBAL
 */
class ConnectionManager implements ConnectionManagerInterface
{

    private static $connections = [];

    /**
     * @param DBConfig $config
     * @param string $name
     * @param int $fetch_mode
     * @return PDO
     */
    public static function createConnection(
        DBConfig $config,
        string $name = 'default',
        int $fetch_mode = PDO::FETCH_ASSOC
    ): PDO
    {
        if (!isset(self::$connections[$name])) {
            try {
                self::$connections[$name] = DatabaseFactory::getConnection($config, $fetch_mode);
            } catch (DBALException $ex) {
                die ($ex);
            }
        }

        return self::$connections[$name];
    }

    /**
     * @param string $name
     * @return null|PDO
     */
    public static function getConnection(string $name = 'default'): ?PDO
    {
        return isset(self::$connections[$name]) ? self::$connections[$name] : null;
    }

    /**
     * @param PDO $pdo
     * @param string $name
     * @return bool
     */
    public static function addConnection(PDO $pdo, string $name): bool
    {
        if (!isset(self::$connections[$name])) {
            self::$connections[$name] = $pdo;
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function dropConnection(string $name): bool
    {
        if (isset(self::$connections[$name])) {
            unset(self::$connections[$name]);
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function connectionExists(string $name): bool
    {
        return isset(self::$connections[$name]);
    }

}