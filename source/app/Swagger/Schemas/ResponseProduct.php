<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ResponseProduct",
 *     title="Response Product",
 *     description="Response schema for product data",
 *     type="object",
 *             properties={
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="deneme"
 *                 ),
 *                 @OA\Property(
 *                     property="price",
 *                     type="number",
 *                     format="float",
 *                     example=199.99
 *                 ),
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         properties={
 *                             @OA\Property(
 *                                 property="category_id",
 *                                 type="integer",
 *                                 example=5
 *                             ),
 *                             @OA\Property(
 *                                 property="name",
 *                                 type="string",
 *                                 example="voluptas"
 *                             ),
 *                             @OA\Property(
 *                                 property="raw",
 *                                 type="string",
 *                                 example="5:voluptas"
 *                             )
 *                         }
 *                     ),
 *                     example={
 *                         {
 *                             "category_id": 5,
 *                             "name": "voluptas",
 *                             "raw": "5:voluptas"
 *                         }
 *                     }
 *                 ),
 *                 @OA\Property(
 *                     property="stock",
 *                     type="integer",
 *                     example=55
 *                 )
 *     }
 * )
 */
class ResponseProduct
{
}
