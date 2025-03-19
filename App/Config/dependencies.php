<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Config\DatabaseFactory;
use App\Repository\UserRepositoryInterface;
use App\Repository\MySQLUserRepository;
use App\Repository\ChargeRepositoryInterface;
use App\Repository\MySQLChargeRepository;
use ParagonIE\EasyDB\EasyDB;

return function (ContainerBuilder $containerBuilder) {
    $dbName = $_ENV['APP_ENV'] === 'test'
        ? $_ENV['MYSQL_TEST_DATABASE']
        : $_ENV['MYSQL_DATABASE'];

        $containerBuilder->addDefinitions([
        EasyDB::class => fn() => DatabaseFactory::create(
            $dbName,
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD']
        ),
        UserRepositoryInterface::class => DI\autowire(MySQLUserRepository::class),
        ChargeRepositoryInterface::class => DI\autowire(MySQLChargeRepository::class),
    ]);
};
