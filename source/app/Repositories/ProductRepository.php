<?php

namespace App\Repositories;

use App\Data\ProductData;
use App\Models\Product;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository.
 *
 * @package namespace App\Repositories;
 */
interface ProductRepository extends RepositoryInterface
{
    public function createProduct(ProductData $data): Product;

    public function getProduct($id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null;

    public function getAllProducts(): \Illuminate\Database\Eloquent\Collection|array;

    public function updateProduct($id, ProductData $productData): Product;


}
