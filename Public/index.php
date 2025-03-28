<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use DI\Bridge\Slim\Bridge;
use Dotenv\Dotenv;
use Slim\Middleware\Session;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(['MYSQL_DATABASE', 'MYSQL_USER', 'MYSQL_PASSWORD'])->notEmpty();

$containerBuilder = new ContainerBuilder();

(require __DIR__ . '/../App/Config/dependencies.php')($containerBuilder);

$container = $containerBuilder->build();

$app = Bridge::create($container);
$app->add(
    new Session([
        'name' => 'basket',
        'autorefresh' => true,
        'lifetime' => '1 hour'
    ])
    );

(require __DIR__ . '/../App/Config/routes.php')($app);

$app->run();
