<?php

namespace App\Repositories\Eloquent;

use App\Data\ProductData;
use App\Models\Product;
use App\Models\StockLocation;
use App\Repositories\ProductRepository;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Product::class;
    }

    public function createProduct(ProductData $data): Product
    {
        $product = $this->model->create([
            'name' => $data->name,
            'sku' => uniqid('SKU'),
            'price' => $data->price,
        ]);

        $product->categories()->attach($data->category);

        $defaultStockLocation = StockLocation::firstOrCreate(['location_name' => 'Default Location']);
        $product->stockLocations()->attach($defaultStockLocation->id, ['quantity' => $data->stock]);

        return $product;
    }

    public function getProduct($id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        return $this->model->with('categories', 'stockLocations')->find($id);
    }

    public function getAllProducts(): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->model->with(['categories'])->get();
    }

    public function updateProduct($id, ProductData $productData): Product
    {
        $product = $this->model->find($id);
        $product->update([
            'name' => $productData->name,
            'price' => $productData->price,
        ]);

        $product->categories()->sync($productData->category);

        $defaultStockLocation = StockLocation::firstOrCreate(['location_name' => 'Default Location']);
        $product->stockLocations()->sync([$defaultStockLocation->id => ['quantity' => $productData->stock]]);

        return $product;
    }


}
