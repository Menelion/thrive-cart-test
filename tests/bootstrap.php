<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\App;
use Dotenv\Dotenv;

function createApp(): App
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    $_ENV['APP_ENV'] = 'test';
    $containerBuilder = new ContainerBuilder();

    $containerBuilder->addDefinitions(
        [
            App::class => function (ContainerInterface $container) {
                AppFactory::setContainer($container);
                
                return AppFactory::create();
            },
        ]
    );
    (require __DIR__ . '/../App/Config/dependencies.php')($containerBuilder);
    $container = $containerBuilder->build();
    $app = $container->get(App::class);
    (require __DIR__ . '/../App/Config/routes.php')($app);

    return $app;
}
