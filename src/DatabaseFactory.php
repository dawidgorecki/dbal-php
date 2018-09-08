<?php

namespace Reven\DBAL;

use PDO;
use PDOException;
use Reven\DBAL\Configuration\Configuration;
use Reven\DBAL\Exceptions\DBALException;

/**
 * Class DatabaseFactory
 * @package Reven\DBAL
 */
class DatabaseFactory
{

    const FETCH_MODE = PDO::FETCH_ASSOC;
    const ERROR_MODE = PDO::ERRMODE_EXCEPTION;

    /**
     * @var DatabaseFactory $instance
     */
    private static $instance;

    /**
     * @var PDO $pdo
     */
    private $pdo;

    private function __construct() {}
    private function __clone() {}

    /**
     * @return DatabaseFactory
     */
    public static function getInstance(): DatabaseFactory
    {
        if (\is_null(self::$instance)) {
            self::$instance = new DatabaseFactory();
        }

        return self::$instance;
    }

    /**
     * @param Configuration $config
     * @return PDO
     * @throws DBALException
     */
    public function getConnection(Configuration $config): PDO
    {
        if (\is_null($this->pdo)) {
            $options = [
                PDO::ATTR_PERSISTENT => $config->isPersistent(),
                PDO::ATTR_ERRMODE => self::ERROR_MODE,
                PDO::ATTR_DEFAULT_FETCH_MODE => self::FETCH_MODE
            ];

            try {
                $this->pdo = new PDO($config->getDsn(), $config->getUsername(), $config->getPassword(), $options);

                switch ($config->getDsn()->getDriver()) {
                    case 'mysql':
                        $this->pdo->exec("SET NAMES " . $config->getCharset());
                        break;

                    case 'pgsql':
                        $this->pdo->exec("SET client_encoding='" . $config->getCharset() . "';");
                        $this->pdo->exec("SET datestyle='DMY';");
                        break;
                }
            } catch (PDOException $ex) {
                throw new DBALException('Connection error', $ex);
            }
        }

        return $this->pdo;
    }

}