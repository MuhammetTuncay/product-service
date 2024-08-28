<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ConsumerController extends Controller
{
    /**
     * Run the product elastic index queue consumer.
     *
     * @return JsonResponse
     */
    public function runProductElasticIndex(): JsonResponse
    {
        try {
            Artisan::call('consumer:product-elastic-index');
            return response()->json(['message' => 'Consumer started successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to start consumer.', 'error' => $e->getMessage()], 500);
        }
    }
}
