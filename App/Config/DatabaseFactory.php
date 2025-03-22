<?php
declare(strict_types=1);
namespace App\Config;

use ParagonIE\EasyDB\Factory as EasyDBFactory;
use ParagonIE\EasyDB\EasyDB;
use PDO;

class DatabaseFactory
{
    public static function create(string $dbName, string $userName, string $password): EasyDB
    {
        return EasyDBFactory::create(
            dsn: sprintf('mysql:host=mariadb;dbname=%s', $dbName),
            username: $userName,
            password: $password,
            options: [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_IGNORE_SPACE => true,
            ]
        );
    }
}
