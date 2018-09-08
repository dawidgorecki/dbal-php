<?php

namespace Reven\DBAL\Exceptions;

use Exception;
use PDOException;

/**
 * Class DBALException
 * @package Reven\DBAL\Exceptions
 */
class DBALException extends Exception
{

    /**
     * @var PDOException
     */
    protected $pdo_exception;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @param string $message
     * @param PDOException|null $pdo_exception
     * @param string|null $sql
     */
    public function __construct(string $message, PDOException $pdo_exception = null, string $sql = null)
    {
        parent::__construct($message, 0);

        if (!is_null($pdo_exception)) {
            $this->pdo_exception = $pdo_exception;
        }

        if (!is_null($sql)) {
            $this->sql = $sql;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $msg = $this->getMessage() . PHP_EOL;

        if (isset($this->pdo_exception)) {
            $pdo_msg = $this->pdo_exception->getMessage();
            $pdo_msg = ucfirst(trim(substr($pdo_msg, strrpos($pdo_msg, ']') + 1)));
            $msg .= 'PDO error: ' . $pdo_msg . '.' . PHP_EOL;
            $msg .= 'Exception code: ' . $this->pdo_exception->getCode() . PHP_EOL;
            $msg .= 'In file: ' . $this->pdo_exception->getFile() . ', line: ' . $this->pdo_exception->getLine() . PHP_EOL;
        } else {
            $msg .= 'In file: ' . $this->getFile() . ', line: ' . $this->getLine() . PHP_EOL;
        }

        if (isset($this->sql)) {
            $msg .= 'SQL: ' . $this->sql . PHP_EOL;
        }

        return $msg;
    }

}