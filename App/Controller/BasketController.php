<?php
declare(strict_types=1);
namespace App\Controller;

use App\Repository\ProductRepositoryInterface;
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
    public function __construct(
        private BasketService $basketService,
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    #[OA\Post(
        path: '/basket/add/{code}',
        summary: 'Add product to basket',
        description: 'Add product to basket',
        tags: ['Basket'],
        responses: [
            '201' => 'Product added to basket',
            '400' => 'Invalid product code'
        ]
    )]
    public function add(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $code = strtolower($request->getAttribute('code'));
        $product = $this->productRepository->findByCode(code: $code, caseSensitive: false);

        if (!$product) {
            return $response->withStatus(
                StatusCodeInterface::STATUS_NOT_FOUND,
                sprintf('Product with code %s not found', $code)
            );
        }

        try {
            $basket = $this->basketService->addProduct($code);
            $response->getBody()->write(json_encode($basket->toArray()));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(StatusCodeInterface::STATUS_CREATED);
        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST, $e->getMessage());
        }
    }

    public function getTotal(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $total = $this->basketService->getTotal();
        $response->getBody()->write(json_encode(['total' => $total]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(StatusCodeInterface::STATUS_OK);
    }
}
