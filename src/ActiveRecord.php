<?php

namespace Reven\DBAL;

use Reven\DBAL\Exceptions\DBALException;
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
     * @var array $config
     */
    protected static $config = [];

    /**
     * @return int|null
     */
    abstract public function getId(): ?int;

    /**
     * @param int $id
     */
    abstract public function setId(int $id): void;

    /**
     * Create and return instance of DBALDatabase
     *
     * @return DBALDatabase
     * @throws DBALException
     */
    protected static function createDBAL(): DBALDatabase
    {
        $conn = ConnectionManager::getConnection(self::getConnectionName());

        if (is_null($conn)) {
            throw new DBALException('Connection "' . self::getConnectionName() . '" does not exists');
        }

        return new DBALDatabase($conn);
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
            try {
                self::$config[$class_name]['database'] = self::createDBAL();
            } catch (DBALException $ex) {
                // TODO: Add better error handling
                die($ex);
            }
        }

        return self::$config[$class_name]['database'];
    }

    /**
     * Return array with object properties
     *
     * @param string $types
     * @param array $skip
     * @return array
     */
    protected function getObjectProperties(string $types = 'private', array $skip = []): array
    {
        $properties = ClassUtils::getProperties($this, $types);
        $result = [];

        foreach ($properties as $propertyName => $reflectionProperty) {
            /**
             * @var \ReflectionProperty $reflectionProperty
             */
            $reflectionProperty->setAccessible(true);
            $propertyValue = $reflectionProperty->getValue($this);

            if (in_array($propertyName, $skip)) {
                continue;
            }

            $propertyName = StringUtils::convertToUnderscored($propertyName);
            $result[$propertyName] = $propertyValue;
        }

        return $result;
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
        $objects = [];

        try {
            $rows = self::getDB()->fetchAll($query, $params);
        } catch (DBALException $ex) {
            // TODO: Add better error handling
            die($ex);
        }

        foreach ($rows as $row) {
            $objects[] = self::instantiation($row);
        }

        return $objects;
    }

    /**
     * Get table row with specified id and return it as object
     *
     * @param int $id
     * @return null|ActiveRecord
     */
    public static function findById(int $id): ?ActiveRecord
    {
        $query = "SELECT * FROM " . self::getTableName() . " WHERE id = :id LIMIT 1";
        $objects = self::findByQuery($query, [":id" => $id]);

        return !empty($objects) ? $objects[0]: null;
    }

    /**
     * Return all table rows as objects array
     *
     * @return array
     */
    public static function findAll(): array
    {
        return self::findByQuery("SELECT * FROM " . self::getTableName());
    }

    /**
     * Create a new record
     *
     * @return bool
     */
    public function create(): bool
    {
        $db = self::getDB();
        $properties = $this->getObjectProperties('private', ["id"]);

        try {
            if ($result = $db->insert(self::getTableName(), $properties)) {
                $this->setId($db->lastId());
            }

            return $result;

        } catch (DBALException $ex) {
            // TODO: Add better error handling
            die($ex);
        }
    }

    /**
     * Update existing record
     *
     * @return bool
     */
    public function update(): bool
    {
        if (is_null($this->getId())) {
            return false;
        }

        $properties = $this->getObjectProperties('private', ["id"]);

        try {
            $affected_rows = self::getDB()->update(self::getTableName(), $properties, ["id" => $this->getId()]);
        } catch (DBALException $ex) {
            // TODO: Add better error handling
            die($ex);
        }

        return ($affected_rows > 0);
    }

    /**
     * Delete record with id specified in object id property
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (is_null($this->getId())) {
            return false;
        }

        try {
            $affected_rows = self::getDB()->delete(self::getTableName(), ["id" => $this->getId()]);
        } catch (DBALException $ex) {
            // TODO: Add better error handling
            die($ex);
        }

        return ($affected_rows > 0);
    }

    /**
     * Create new record or update existing if object id has been set
     *
     * @return bool
     */
    public function save(): bool
    {
        return is_null($this->getId()) ? $this->create() : $this->update();
    }

}