<?php

namespace Reven\DBAL;

/**
 * Class ActiveRecord
 * @package Reven\DBAL
 */
class ActiveRecord implements CRUDInterface
{
    
    public static function findById(int $id): ?ActiveRecord
    {
        // TODO: Implement findById() method.
    }

    public static function findAll(): array
    {
        // TODO: Implement findAll() method.
    }

    public static function findByQuery(string $query, array $params = []): array
    {
        // TODO: Implement findByQuery() method.
    }

    public function create(): bool
    {
        // TODO: Implement create() method.
    }

    public function update(): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(): bool
    {
        // TODO: Implement delete() method.
    }

}