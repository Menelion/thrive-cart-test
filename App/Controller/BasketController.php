<?php
declare(strict_types=1);
namespace App\Controller;

use App\Service\BasketService;
use Fig\Http\Message\StatusCodeInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

#[OA\Info(
    title: "Basket API",
    version: "1.0.0",
    description: "API for managing baskets"
)]
class BasketController
{
    public function __construct(private BasketService $basketService)
    {
    }

}
