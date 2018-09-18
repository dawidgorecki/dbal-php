<?php

namespace Reven\DBAL;

use PDO;
use PDOException;
use PDOStatement;
use Reven\DBAL\Exceptions\DBALException;

/**
 * Class DBALDatabase
 * @package Reven\DBAL
 */
class DBALDatabase
{

    /**
     * @var bool $exceptions
     */
    protected $exceptions;

    /**
     * @var string $last_error
     */
    protected $last_error;

    /**
     * @var string $query_string
     */
    protected $query_string;

    /**
     * @var PDO $pdo
     */
    protected $pdo;

    /**
     * DBALDatabase constructor.
     * @param PDO $pdo
     * @param bool $exceptions
     */
    public function __construct(PDO $pdo, bool $exceptions = true)
    {
        $this->pdo = $pdo;
        $this->exceptions = $exceptions;
    }

    /**
     * Return a PDO instance representing a connection to a database
     *
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Returns last error message
     *
     * @return string
     */
    public function getLastError(): string
    {
        return $this->last_error ?? '';
    }

    /**
     * Returns query used in the last SQL statement
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->query_string ?? '';
    }

    /**
     * Initiates a transaction
     *
     * @return bool TRUE on success or FALSE on failure
     * @throws DBALException If there is already a transaction started or
     * the driver does not support transactions
     */
    public function startTransaction(): bool
    {
        try {
            return $this->getPDO()->beginTransaction();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Transaction error', $ex);
            }

            return false;
        }
    }

    /**
     * Commits a transaction
     *
     * @return bool TRUE on success or FALSE on failure
     * @throws DBALException If there is no active transaction
     */
    public function commit(): bool
    {
        try {
            return $this->getPDO()->commit();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Transaction error', $ex);
            }

            return false;
        }
    }

    /**
     * Rolls back the current transaction
     *
     * @return bool TRUE on success or FALSE on failure
     * @throws DBALException If there is no active transaction
     */
    public function rollback(): bool
    {
        try {
            return $this->getPDO()->rollBack();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Transaction error', $ex);
            }

            return false;
        }
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * @param string $query
     * @param array $params
     * @param int|null $fetch_mode
     * @return mixed
     * @throws DBALException On failure
     */
    public function fetchAll(string $query, array $params = [], int $fetch_mode = null)
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            return $pdo_stmt->fetchAll($fetch_mode);
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return false;
        }
    }

    /**
     * Return first row of the query result
     *
     * @param string $query
     * @param array $params
     * @param int|null $fetch_mode
     * @return mixed
     * @throws DBALException On failure
     */
    public function fetchFirst(string $query, array $params = [], int $fetch_mode = null)
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            return $pdo_stmt->fetch($fetch_mode);
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return false;
        }
    }

    /**
     * Return first row of the query result as numeric indexed array
     *
     * @param string $query
     * @param array $params
     * @return array
     * @throws DBALException On failure
     */
    public function fetchArray(string $query, array $params = []): array
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            $row = $pdo_stmt->fetch(PDO::FETCH_NUM);
            return (!$row) ? [] : $row;
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return [];
        }
    }

    /**
     * Return first row of the query result as associative array
     *
     * @param string $query
     * @param array $params
     * @return array
     * @throws DBALException On failure
     */
    public function fetchAssoc(string $query, array $params = []): array
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            $row = $pdo_stmt->fetch(PDO::FETCH_ASSOC);
            return (!$row) ? [] : $row;
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return [];
        }
    }

    /**
     * Returns a single column from the first row of the query result
     *
     * @param string $query
     * @param array $params
     * @param int $column_number
     * @return string
     * @throws DBALException On failure
     */
    public function fetchColumn(string $query, array $params = [], int $column_number = 0): string
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);

            $col = $pdo_stmt->fetchColumn($column_number);
            return (!$col) ? '': $col;
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return '';
        }
    }

    /**
     * Delete rows of a given table
     *
     * @param string $table_name
     * @param array $condition
     * @return int Number of deleted rows
     * @throws DBALException On failure
     */
    public function delete(string $table_name, array $condition): int
    {
        $column_name = array_keys($condition)[0];
        $sql = 'DELETE FROM ' . $table_name . ' WHERE ' . $column_name . '=?';

        try {
            $pdo_stmt = $this->getPDO()->prepare($sql);
            $pdo_stmt->execute([$condition[$column_name]]);
            return $pdo_stmt->rowCount();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Row(s) cannot be deleted', $ex, $sql);
            }

            return 0;
        }
    }

    /**
     * Insert a row into the given table
     *
     * @param string $table_name
     * @param array $params
     * @return bool TRUE if row is inserted, otherwise FALSE
     * @throws DBALException On failure
     */
    public function insert(string $table_name, array $params): bool
    {
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = 'INSERT INTO ' . $table_name . '(' . implode(',', array_keys($params)) . ') ';
        $sql .= 'VALUES(' . $placeholders . ')';

        $this->query_string = $sql;

        try {
            $pdo_stmt = $this->getPDO()->prepare($sql);
            $pdo_stmt->execute(array_values($params));
            return ($pdo_stmt->rowCount() > 0);
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Row cannot be inserted', $ex, $sql);
            }

            return false;
        }
    }

    /**
     * Update rows of a given table
     *
     * @param string $table_name
     * @param array $params
     * @param array $condition
     * @return int Number of updated rows
     * @throws DBALException On failure
     */
    public function update(string $table_name, array $params, array $condition): int
    {
        $pairs = [];

        foreach ($params as $key => $value) {
            array_push($pairs, $key . '=?');
        }

        $column_name = array_keys($condition)[0];
        $values = array_values($params);
        array_push($values, $condition[$column_name]);

        $sql = 'UPDATE ' . $table_name . ' SET ' . implode(',', $pairs) . ' WHERE ' . $column_name . '=?';
        $this->query_string = $sql;

        try {
            $pdo_stmt = $this->getPDO()->prepare($sql);
            $pdo_stmt->execute($values);
            return $pdo_stmt->rowCount();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Row(s) cannot be updated', $ex, $sql);
            }

            return 0;
        }
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string $string
     * @param int $parameter_type
     * @return string
     * @throws DBALException If driver does not support quoting
     */
    public function quote(string $string, int $parameter_type): string
    {
        $quoted = $this->getPDO()->quote($string, $parameter_type);

        if ($quoted === false) {
            $this->last_error = "Driver does not support quoting";

            if ($this->exceptions) {
                throw new DBALException('Driver does not support quoting');
            }
        }

        return $quoted ? $quoted : '';
    }

    /**
     * Executes a prepared statement with the given SQL and parameters and returns PDOStatement instance
     *
     * @param string $query
     * @param array $params
     * @return null|PDOStatement
     * @throws DBALException
     */
    public function executeQuery(string $query, array $params = []): ?PDOStatement
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            return $pdo_stmt;
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return null;
        }
    }

    /**
     * Executes a prepared statement with the given SQL and parameters and returns the affected rows count
     *
     * @param string $query
     * @param array $params
     * @return int
     * @throws DBALException
     */
    public function updateQuery(string $query, array $params = []): int
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            $pdo_stmt->execute($params);
            return $pdo_stmt->rowCount();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Query cannot be executed', $ex, $query);
            }

            return 0;
        }
    }

    /**
     * Prepare a given SQL statement and return the PDOStatement instance
     *
     * @param string $query
     * @return null|PDOStatement
     * @throws DBALException
     */
    public function prepare(string $query): ?PDOStatement
    {
        $this->query_string = $query;

        try {
            $pdo_stmt = $this->getPDO()->prepare($query);
            return $pdo_stmt;
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Cannot prepare the statement', $ex, $query);
            }

            return null;
        }
    }

    /**
     * Return ID of the last inserted row
     *
     * @return string
     * @throws DBALException
     */
    public function lastId(): string
    {
        try {
            return $this->getPDO()->lastInsertId();
        } catch (PDOException $ex) {
            $this->last_error = $ex->getMessage();

            if ($this->exceptions) {
                throw new DBALException('Cannot get id, lastval is not yet defined', $ex);
            }

            return '';
        }
    }

}