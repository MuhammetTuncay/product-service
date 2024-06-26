<?php

namespace App\Http\Controllers;

class SwaggerController extends Controller
{
    /**
     *
     * @OA\Info(
     *     version="V1",
     *     title="Product API",
     *     @OA\Server(
     *          url="http://localhost:8080",
     *          description="Local server"
     *     )
     * )
     *
     */
    public function __invoke()
    {
    }
}
