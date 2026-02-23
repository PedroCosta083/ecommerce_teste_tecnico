<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'E-commerce API',
)]
#[OA\Server(url: 'http://localhost:8000', description: 'Local server')]
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
}
