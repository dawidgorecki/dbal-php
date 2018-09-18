<?php

namespace Reven\DBAL;

use PDO;
use PDOException;
use Reven\DBAL\Configuration\DBConfig;
use Reven\DBAL\Configuration\DSN;
use Reven\DBAL\Exceptions\DBALException;

/**
 * Class DatabaseFactory
 * @package Reven\DBAL
 */
class DatabaseFactory implements DatabaseFactoryInterface
{

    const ERROR_MODE = PDO::ERRMODE_EXCEPTION;

    /**
     * Creates and return a PDO instance representing a connection to a database
     *
     * @param DBConfig $config
     * @param int $fetch_mode
     * @return PDO
     * @throws DBALException
     */
    public static function getConnection(DBConfig $config, int $fetch_mode = PDO::FETCH_ASSOC): PDO
    {
        $options = [
            PDO::ATTR_PERSISTENT => $config->isPersistent(),
            PDO::ATTR_ERRMODE => self::ERROR_MODE,
            PDO::ATTR_DEFAULT_FETCH_MODE => $fetch_mode
        ];

        try {
            $conn = new PDO($config->getDsn(), $config->getUsername(), $config->getPassword(), $options);

            switch ($config->getDsn()->getDriver()) {
                case DSN::DRIVER_MYSQL:
                    $conn->exec("SET NAMES " . $config->getCharset());
                    break;

                case DSN::DRIVER_PGSQL:
                    $conn->exec("SET client_encoding='" . $config->getCharset() . "';");
                    $conn->exec("SET datestyle='DMY';");
                    break;
            }
        } catch (PDOException $ex) {
            throw new DBALException('Database connection error', $ex);
        }

        return $conn;
    }

}