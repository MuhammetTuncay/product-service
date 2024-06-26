<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"id", "name", "sku", "price", "created_at", "updated_at", "categories"},
 *     @OA\Property(property="id", type="integer", example=100),
 *     @OA\Property(property="name", type="string", example="deneme"),
 *     @OA\Property(property="sku", type="string", example="SKU6673957e4b2f4"),
 *     @OA\Property(property="price", type="number", format="float", example=199.99),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-20T02:35:42.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-20T02:35:42.000000Z"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     )
 * )
 */
class Product {}

