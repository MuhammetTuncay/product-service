<?php

namespace App\Enums;

enum ProductIndexQueueEnum: string
{
    case PRODUCT_INDEX_QUEUE = 'product_index_queue';

    public function getQueueName(): string
    {
        return $this->value;
    }
}
