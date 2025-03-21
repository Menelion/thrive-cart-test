<?php
declare(strict_types=1);
namespace App\Model;

use App\Value\ProductPrice;

class Product
{
    public function __construct(
        public string $code,
        public string $name,
        public ProductPrice $price,
    )
    {
    }

    /** @param mixed[] $data */
    public static function fromArray(array $data): self
    {
        $priceInCents = (int) ($data['price'] * 100);

        return new self(
            code: $data['code'],
            name: $data['name'],
            price: new ProductPrice($priceInCents),
        );
    }

    /** return mixed[] */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'price' => $this->price->toDollars(),
        ];
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): ProductPrice
    {
        return $this->price;
    }
}
