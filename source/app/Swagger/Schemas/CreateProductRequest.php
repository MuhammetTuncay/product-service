<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="CreateProductRequest",
 *     type="object",
 *     required={"name", "price", "stock"},
 *     @OA\Property(property="name", type="string", example="deneme"),
 *     @OA\Property(property="category", type="array", nullable=true, @OA\Items(type="integer"), description="Array of category IDs"),
 *     @OA\Property(property="price", type="number", format="float", example=199.99),
 *     @OA\Property(property="stock", type="integer", example=55)
 * )
 */
class CreateProductRequest {}
