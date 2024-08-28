<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ResponseElasticProduct",
 *     title="ResponseElasticProduct",
 *     description="Response schema for products fetched from Elasticsearch",
 *     required={"product_id", "name", "price", "sku", "category"},
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         format="int64",
 *         description="Product ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Product name",
 *         example="Product name"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Product price",
 *         example=10.5
 *     ),
 *     @OA\Property(
 *         property="sku",
 *         type="string",
 *         description="Product SKU",
 *         example="SKU123"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="array",
 *         description="Product categories",
 *         @OA\Items(
 *             type="object",
 *             required={"category_id", "name", "raw"},
 *             @OA\Property(
 *                 property="category_id",
 *                 type="integer",
 *                 format="int64",
 *                 description="Category ID",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Category name",
 *                 example="Category name"
 *             ),
 *             @OA\Property(
 *                 property="raw",
 *                 type="string",
 *                 description="Raw category data",
 *                 example="1:Category name"
 *             )
 *         )
 *     )
 * )
 */
class ResponseElasticProduct {}
