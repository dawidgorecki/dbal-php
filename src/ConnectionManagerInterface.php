<?php

namespace Reven\DBAL;

use PDO;
use Reven\DBAL\Configuration\DBConfig;

/**
 * Interface ConnectionManagerInterface
 * @package Reven\DBAL
 */
interface ConnectionManagerInterface
{
    public static function createConnection(DBConfig $config, string $name = 'default', int $fetch_mode = PDO::FETCH_ASSOC): PDO;

    public static function getConnection(string $name = 'default'): ?PDO;
}