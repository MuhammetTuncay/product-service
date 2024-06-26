<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     required={"id", "name", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="voluptas"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T12:50:46.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T12:50:46.000000Z"),
 *     @OA\Property(
 *         property="pivot",
 *         type="object",
 *         @OA\Property(property="product_id", type="integer", example=100),
 *         @OA\Property(property="category_id", type="integer", example=5)
 *     )
 * )
 */
class Category {}
