<?php

namespace Reven\DBAL;

use PDO;
use Reven\DBAL\Configuration\DBConfig;

/**
 * Interface DatabaseFactoryInterface
 * @package Reven\DBAL
 */
interface DatabaseFactoryInterface
{
    public static function getConnection(DBConfig $config): PDO;
}