<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Config\DatabaseFactory;
use App\Repository\ProductRepositoryInterface;
use App\Repository\InMemoryProductRepository;
use App\Strategy\DiscountStrategy;
use App\Strategy\RedWidgetDiscount;
use ParagonIE\EasyDB\EasyDB;

return function (ContainerBuilder $containerBuilder)
{
    $dbName = $_ENV['APP_ENV'] === 'test'
        ? $_ENV['MYSQL_TEST_DATABASE']
        : $_ENV['MYSQL_DATABASE'];

        $containerBuilder->addDefinitions([
        EasyDB::class => fn() => DatabaseFactory::create(
            $dbName,
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD']
        ),
        ProductRepositoryInterface::class => DI\autowire(InMemoryProductRepository::class),
        DiscountStrategy::class => DI\autowire(RedWidgetDiscount::class),
    ]);
};
