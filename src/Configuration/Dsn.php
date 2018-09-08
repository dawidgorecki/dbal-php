<?php

namespace Reven\DBAL\Configuration;

use Reven\DBAL\Exceptions\DriverException;

/**
 * Class Dsn
 * @package Reven\DBAL\Configuration
 */
class Dsn
{

    const SUPPORTED_DRIVERS = ['mysql', 'pgsql'];

    /**
     * @var string $driver
     */
    private $driver;

    /**
     * @var string $db_name
     */
    private $db_name;

    /**
     * @var string $host
     */
    private $host;

    /**
     * @var int $port
     */
    private $port;

    /**
     * @param string $driver
     * @param string $db_name
     * @param string $host
     * @param int $port
     * @throws DriverException
     */
    public function __construct(string $driver, string $db_name, string $host, int $port)
    {
        if (!\in_array($driver, self::SUPPORTED_DRIVERS, true)) {
            throw new DriverException('Driver is not supported');
        }

        $this->driver = $driver;
        $this->db_name = $db_name;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->db_name;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Return DSN string
     *
     * @return string
     */
    public function __toString(): string
    {
        $dsn =
            $this->getDriver() . ':dbname=' .
            $this->getDbName() . ';host=' .
            $this->getHost() . ';port=' .
            $this->getPort();

        return $dsn;
    }


}