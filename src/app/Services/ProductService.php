<?php

namespace App\Services;


use App\Builder\ElasticSearchQueryBuilder;
use App\Clients\ElasticsearchClient;
use App\Data\ProductData;
use App\Data\SearchFilterData;
use App\Enums\ProductIndexQueueEnum;
use App\Managers\RabbitMQManager;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Redis;

class ProductService
{
    protected ProductRepository $productRepository;

    protected ElasticsearchClient $elasticsearchClient;

    protected ElasticSearchQueryBuilder $elasticSearchQueryBuilder;

    protected RabbitMQManager $rabbitMQManager;


    public function __construct(
        ProductRepository         $productRepository,
        ElasticsearchClient       $elasticsearchClient,
        ElasticSearchQueryBuilder $elasticSearchQueryBuilder,
        RabbitMQManager           $rabbitMQManager
    )
    {
        $this->productRepository = $productRepository;
        $this->elasticsearchClient = $elasticsearchClient;
        $this->elasticSearchQueryBuilder = $elasticSearchQueryBuilder;
        $this->rabbitMQManager = $rabbitMQManager;
    }

    public function createProduct(ProductData $data): Product
    {
        return $this->productRepository->createProduct($data);
    }


    public function indexProductInElasticsearch(Product $product): bool
    {
        $params = [
            'index' => 'products',
            'id' => $product->id,
            'body' => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sku' => $product->sku,
                'category' => $product->categories->map(function ($category) {
                    return [
                        'category_id' => $category->id,
                        'name' => $category->name,
                        'raw' => $category->id . ':' . $category->name,
                    ];
                })->toArray(),
            ],
        ];

        $this->elasticsearchClient->index($params);

        return true;
    }

    public function createMapping(): \Illuminate\Http\JsonResponse
    {
        $response = $this->elasticsearchClient->createMappingAndSetting();
        if ($response->exception === null) {
            return response()->json(['message' => 'Mapping created successfully']);
        }
        return response()->json(['response' => $response]);
    }


    public function saveProductInRedis(Product $product, ProductData $productData): void
    {
        Redis::set("product:{$product->id}", json_encode([
            'name' => $product->name,
            'price' => $product->price,
            'category' => $product->categories->map(function ($category) {
                return [
                    'category_id' => $category->id,
                    'name' => $category->name,
                    'raw' => $category->id . ':' . $category->name,
                ];
            }),
            'stock' => $productData->stock,
        ]));
    }

    public function listProducts(SearchFilterData $searchFilterData): array
    {
        $params = $this->elasticSearchQueryBuilder->build($searchFilterData);

        try {
            $response = $this->elasticsearchClient->search($params);
            return $this->processElasticResponse($response);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function processElasticResponse($response): array
    {
        $data = [];

        foreach ($response['hits']['hits'] as $product) {
            $data[] = [
                'product_id' => $product['_source']['product_id'],
                'name' => $product['_source']['name'],
                'price' => $product['_source']['price'],
                'sku' => $product['_source']['sku'],
                'category' => $product['_source']['category'],
            ];
        }

        return $data;
    }


    public function showProduct($id)
    {
        $product = Redis::get("product:{$id}");
        if ($product) {
            return json_decode($product);
        }
        return $this->transformDatabaseProductToRedisFormat($this->productRepository->getProduct($id));
    }

    protected function transformDatabaseProductToRedisFormat($dbProduct): array
    {
        if (!$dbProduct) return [];
        return [
            'name' => $dbProduct->name,
            'price' => (float)$dbProduct->price,
            'category_id' => array_map(function ($category) {
                return [
                    'category_id' => $category['id'],
                    'name' => $category['name'],
                    'raw' => "{$category['id']}:{$category['name']}",
                ];
            }, $dbProduct->categories->toArray()),
            'stock' => $dbProduct->stockLocations[0]['pivot']['quantity'] ?? 0,

        ];
    }


    public function updateProduct($id, ProductData $productData): Product
    {
        $product = $this->productRepository->updateProduct($id, $productData);
        $this->saveProductInRedis($product, $productData);
        $this->indexProductInElasticsearch($product);
        return $product;
    }

    public function deleteProduct($id): void
    {
        Redis::delete("product:{$id}");

        $this->elasticsearchClient->delete([
            'index' => 'products',
            'id' => $id,
        ]);

        $this->productRepository->delete($id);
    }

    public function productsBulkIndex(): \Illuminate\Http\JsonResponse
    {
        $products = $this->productRepository->getAllProducts();
        foreach ($products as $product) {
            $this->rabbitMQManager->publish(
                queue: ProductIndexQueueEnum::PRODUCT_INDEX_QUEUE->getQueueName(),
                message: json_encode($product),
            );
        }

        return response()->json(['message' => 'Products indexed successfully']);
    }

}
