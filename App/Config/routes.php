<?php
declare(strict_types=1);

use App\Controller\BasketController;
use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    $app->any('/basket/add/{code}', [BasketController::class, 'add']);
    $app->get('/basket/total', [BasketController::class, 'getTotal']);
    $app->get('/api-docs', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $openapi = Generator::scan([__DIR__ . '/../Controller']);
        $response->getBody()->write($openapi->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });
    $app->redirect('/', '/swagger-ui/', 301);
};
