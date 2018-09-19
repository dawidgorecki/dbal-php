<?php

namespace Reven\DBAL;

/**
 * Interface CRUDInterface
 * @package Reven\DBAL
 */
interface CRUDInterface
{
    public static function findById(int $id): ?ActiveRecord;

    public static function findAll(): array;

    public static function findByQuery(string $query, array $params = []): array;

    public function create(): bool;

    public function update(): bool;

    public function delete(): bool;
}