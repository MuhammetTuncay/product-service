<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public string $name,
        public float $price,
        public int $stock,
        public ?array $category = null,
    )
    {
    }
}
