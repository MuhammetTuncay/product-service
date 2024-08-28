<?php

namespace App\Http\Controllers;

use App\Data\SearchFilterData;
use App\Http\Requests\CreateProductFilterRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Resources\CreateAndUpdateProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductListResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param CreateProductFilterRequest $request
     * @return ProductListResource
     *
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Product"},
     *     summary="List products",
     *     description="Retrieve a list of products with optional filtering and pagination.",
     *     operationId="listProducts",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query to filter products by name.",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="product name"
     *     ),
     *     @OA\Parameter(
     *         name="product_ids",
     *         in="query",
     *         description="Comma-separated list of product IDs to filter by.",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="1,2,3"
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Comma-separated list of category IDs to filter by.",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="1,2,3"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination (default: 1).",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         example=1
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page (default: 10).",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         example=10
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Field name to sort results by.",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="product_id"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order ('asc' or 'desc').",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}),
     *         example="desc"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products matching the criteria.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ResponseElasticProduct")
     *         )
     *     )
     * )
     */

    public function list(CreateProductFilterRequest $request): ProductListResource
    {
        $searchFilterData = new SearchFilterData(
            query: $request->get('query'),
            product_ids: $request->get('product_ids'),
            category: $request->get('category'),
            page: $request->get('page', 1),
            limit: $request->get('limit', 10),
            sort: $request->get('sort', 'product_id'),
            order: $request->get('order', 'desc'),
        );
        return new ProductListResource($this->productService->listProducts($searchFilterData));
    }

    /**
     * @param CreateProductRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/products",
     *      tags={"Product"},
     *      summary="Create a new product",
     *      description="Create a new product",
     *      operationId="createProduct",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CreateProductRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Product created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product created successfully"),
     *              @OA\Property(property="product", type="object", ref="#/components/schemas/Product")
     *          )
     *      )
     *  )
     */
    public function create(CreateProductRequest $request): JsonResponse
    {
        $productData = $request->data();
        $product = $this->productService->createProduct($productData);

        $this->productService->indexProductInElasticsearch($product);
        $this->productService->saveProductInRedis($product, $productData);

        return response()->json(
            [
                'message' => 'Product created successfully',
                'product' => CreateAndUpdateProductResource::make($product),
            ],
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * @param $id
     * @return ProductDetailResource
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *     summary="Get product by id",
     *     description="Get product by id",
     *     operationId="showProduct",
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of product to return",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Product found",
     *     @OA\JsonContent(
     *     @OA\Property(property="product", type="object", ref="#/components/schemas/ResponseProduct")
     *   )
     * ),
     *     @OA\Response(
     *     response=404,
     *     description="Product not found",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Product not found")
     *  )
     * )
     * )
     */
    public function show($id): ProductDetailResource
    {
        $product = $this->productService->showProduct($id);
        if (!$product) {
            abort(ResponseAlias::HTTP_NOT_FOUND, 'Product not found');
        }
        return new ProductDetailResource(collect($product));
    }

    /**
     * @param CreateProductRequest $request
     * @param $id
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *     summary="Update product by id",
     *     description="Update product by id",
     *     operationId="updateProduct",
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of product to update",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     * ),
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/CreateProductRequest")
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Product updated successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Product updated successfully"),
     *     @OA\Property(property="product", type="object", ref="#/components/schemas/Product")
     *  )
     * )
     * )
     */
    public function update(CreateProductRequest $request, $id): JsonResponse
    {
        $productData = $request->data();
        $product = $this->productService->updateProduct($id, $productData);

        return response()->json(
            [
                'message' => 'Product updated successfully',
                'product' => CreateAndUpdateProductResource::make($product),
            ],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *     summary="Delete product by id",
     *     description="Delete product by id",
     *     operationId="deleteProduct",
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of product to delete",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Product deleted successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Product deleted successfully")
     *  )
     * )
     * )
     */
    public function delete($id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * @return JsonResponse
     *
     * @OA\Post (
     *     path="/api/products-index",
     *     tags={"Product"},
     *     summary="Index all products in Elasticsearch",
     *     description="Index all products in Elasticsearch",
     *     operationId="productsBulkIndex",
     *     @OA\Response(
     *     response=200,
     *     description="Products indexed successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Products indexed successfully")
     * )
     * )
     * )
     *
     */
    public function productsBulkIndex(): JsonResponse
    {
        return $this->productService->productsBulkIndex();
    }

    /**
     * Create mapping for Elasticsearch
     *
     * @return JsonResponse
     *
     * @OA\PathItem(
     *     path="/api/create-mapping",
     *     @OA\Get(
     *     tags={"Product"},
     *     summary="Create mapping for Elasticsearch",
     *     description="Create mapping for Elasticsearch",
     *     operationId="createMapping",
     *     @OA\Response(
     *     response=200,
     *     description="Mapping created successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Mapping created successfully")
     *    )
     *  )
     * )
     * )
     */
    public function createMapping(): JsonResponse
    {
        return $this->productService->createMapping();
    }
}

