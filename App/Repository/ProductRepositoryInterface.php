<?php
declare(strict_types=1);
namespace App\Repository;

use App\Model\Product;

interface ProductRepositoryInterface
{
    public function findByCode(string $code, bool $caseSensitive): ?Product;
}
