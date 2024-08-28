<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SearchFilterData extends Data
{
    public function __construct(
        public ?string         $query,
        public ?string         $product_ids,
        public int|string|null $category = null,
        public int             $page = 1,
        public int             $limit = 10,
        public string          $sort = 'product_id',
        public string          $order = 'desc',
    )
    {
    }
}
