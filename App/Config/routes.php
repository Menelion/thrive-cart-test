<?php
declare(strict_types=1);

use App\Controller\UserController;
use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    $app->post('/user', [UserController::class, 'createUser']);
    $app->delete('/user/{id}', [UserController::class, 'deleteUser']);
    $app->post('/user/{id}/charge', [UserController::class, 'addCharge']);
    $app->get('/user/{id}/statistics', [UserController::class, 'getChargeStatistics']);
    $app->get('/api-docs', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $openapi = Generator::scan([__DIR__ . '/../Controller']);
        $response->getBody()->write($openapi->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });
    $app->redirect('/', '/swagger-ui/', 301);
};
