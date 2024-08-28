<?php

use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('/create-mapping', [ProductController::class, 'createMapping']);
    Route::post('/products-index', [ProductController::class, 'productsBulkIndex']);
    Route::get('/run-consumer', [ConsumerController::class, 'runProductElasticIndex']); //supervisor ile çalıştırılacak zaten ama conf dosyasında numprocs 0 yaparsan manuel çalıştırabilirsin.

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'list']);

        Route::post('/', [ProductController::class, 'create']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'delete']);
    });
});



