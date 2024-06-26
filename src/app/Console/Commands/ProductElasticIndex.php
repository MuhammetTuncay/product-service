<?php

namespace App\Console\Commands;

use App\Enums\ProductIndexQueueEnum;
use App\Managers\RabbitMQManager;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ProductElasticIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer:product-elastic-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQManager $rabbitMQManager, ProductService $productService)
    {
        $consumer = [
            ProductIndexQueueEnum::PRODUCT_INDEX_QUEUE->getQueueName() => function ($message) use ($productService) {
                $message = json_decode($message->body, true);
                if (is_array($message)) {
                    $productService->indexProductInElasticsearch(
                        product: Product::fromMessageArray($message)
                    );
                }
            }
        ];

        $rabbitMQManager->multiConsume(
            consume: $consumer
        );

        return CommandAlias::SUCCESS;
    }
}
