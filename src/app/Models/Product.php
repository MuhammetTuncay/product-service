<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'price'];

    public static function searchFilterParams(): array
    {

        return [
            'category' => [
                'key'   => 'category', //virgül ile prefix artabilir.
                'rule' => 'regex:/(^[0-9,]+$)+/',
            ],
            'query' => [
                'key'   => 'query',
                'rule' => 'regex:/(^[a-zA-Z0-9, .ÇçÖöŞşİıĞğÖöÜü;]+$)+/',
            ],
            'product_ids' => [
                'key'   => 'product_ids',
                'rule' => 'regex:/(^[0-9,]+$)+/',
            ],
            'page' => [
                'key'   => 'page',
                'rule' => 'numeric',
            ],
            'limit' => [
                'key'   => 'limit',
                'rule' => 'numeric',
            ],
            'sort' => [
                'key'   => 'sort',
                'rule' => 'regex:/([a-zA-Z0-9, ÇçÖöŞşİıĞğÖöÜü]+$)+/',
            ],
            'order' => [
                'key'   => 'order',
                'rule' => 'regex:/([a-zA-Z0-9, ÇçÖöŞşİıĞğÖöÜü]+$)+/',
            ],
        ];

    }

    public static function fromMessageArray(array $message): Product
    {
        $product = new static();
        $product->id = $message['id'];
        $product->name = $message['name'];
        $product->sku = $message['sku'];
        $product->price = $message['price'];

        $categories = [];
        foreach ($message['categories'] as $categoryData) {
            $category = new Category([
                'id' => $categoryData['id'],
                'name' => $categoryData['name'],
                'raw' => $categoryData['id'] . ':' . $categoryData['name'],
            ]);
            $categories[] = $category;
        }
        $product->categories()->saveMany($categories);
        return $product;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function stockLocations(): BelongsToMany
    {
        return $this->belongsToMany(StockLocation::class, 'product_stock')->withPivot('quantity');
    }
}

