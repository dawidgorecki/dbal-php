<?php

namespace Reven\DBAL;

use Reven\DBAL\Utils\ClassUtils;
use Reven\DBAL\Utils\InflectUtils;
use Reven\DBAL\Utils\StringUtils;

/**
 * Class ActiveRecord
 * @package Reven\DBAL
 */
abstract class ActiveRecord implements CRUDInterface
{

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var array $config
     */
    protected static $config = [];

    /**
     * Create and return instance of DBALDatabase
     *
     * @return DBALDatabase
     */
    protected static function createDBAL(): DBALDatabase
    {
        return new DBALDatabase(ConnectionManager::getConnection(self::getConnectionName()));
    }

    /**
     * Create an object from single row of table
     *
     * @param array $row
     * @return ActiveRecord
     */
    protected static function instantiation(array $row): ActiveRecord
    {
        $class_name = get_called_class();
        $model = new $class_name;
        ClassUtils::callSetters($model, $row);

        return $model;
    }

    /**
     * Return name of the database table
     *
     * @return string
     */
    public static function getTableName(): string
    {
        $class_name = get_called_class();

        if (!isset(self::$config[$class_name]['table_name'])) {
            $model_name_parts = StringUtils::splitByCapitalLetter(ClassUtils::getShortName($class_name));
            $last = count($model_name_parts) - 1;
            $model_name_parts[$last] = InflectUtils::pluralize($model_name_parts[$last]);

            self::$config[$class_name]['table_name'] = strtolower(implode('_', $model_name_parts));
        }

        return self::$config[$class_name]['table_name'];
    }

    /**
     * Set name of the database table
     *
     * @param string $table_name
     */
    public static function setTableName(string $table_name): void
    {
        $class_name = get_called_class();
        self::$config[$class_name]['table_name'] = $table_name;
    }

    /**
     * Return name of the connection used by model
     *
     * @return string
     */
    public static function getConnectionName(): string
    {
        $class_name = get_called_class();

        if (!isset(self::$config[$class_name]['connection_name'])) {
            self::$config[$class_name]['connection_name'] = 'default';
        }

        return self::$config[$class_name]['connection_name'];
    }

    /**
     * Set name of the existing connection
     *
     * @param string $connection_name
     */
    public static function setConnectionName(string $connection_name): void
    {
        $class_name = get_called_class();
        self::$config[$class_name]['connection_name'] = $connection_name;
    }

    /**
     * Return instance of DBALDatabase used by model
     *
     * @return DBALDatabase
     */
    protected static function getDB(): DBALDatabase
    {
        $class_name = get_called_class();

        if (!isset(self::$config[$class_name]['database'])) {
            self::$config[$class_name]['database'] = self::createDBAL();
        }

        return self::$config[$class_name]['database'];
    }

    /**
     * Execute query and return array of objects
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public static function findByQuery(string $query, array $params = []): array
    {
        // TODO: Implement findByQuery() method.
    }

    /**
     * Get table row with specified id and return it as object
     *
     * @param int $id
     * @return null|ActiveRecord
     */
    public static function findById(int $id): ?ActiveRecord
    {
        // TODO: Implement findById() method.
    }

    /**
     * Return all table rows as objects array
     *
     * @return array
     */
    public static function findAll(): array
    {
        // TODO: Implement findAll() method.
    }

    /**
     * Create a new record
     *
     * @return bool
     */
    public function create(): bool
    {
        // TODO: Implement create() method.
    }

    /**
     * Update existing record
     *
     * @return bool
     */
    public function update(): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete record with id specified in object id property
     *
     * @return bool
     */
    public function delete(): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * Create new record or update existing if object id has been set
     *
     * @return bool
     */
    public function save(): bool
    {
        // TODO: Implement save() method.
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}