<?php

namespace Reven\DBAL\Configuration;

/**
 * Class Configuration
 * @package Reven\DBAL\Configuration
 */
class Configuration
{

    /**
     * @var Dsn $dsn
     */
    private $dsn;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $charset
     */
    private $charset;

    /**
     * @var bool $persistent
     */
    private $persistent;

    /**
     * @param Dsn $dsn
     * @param string $username
     * @param string $password
     * @param string $charset
     * @param bool $persistent
     */
    public function __construct(
        Dsn $dsn,
        string $username,
        string $password,
        string $charset = 'utf8',
        bool $persistent = false)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        $this->persistent = $persistent;
    }

    /**
     * @return Dsn
     */
    public function getDsn(): Dsn
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @return bool
     */
    public function isPersistent(): bool
    {
        return $this->persistent;
    }

}