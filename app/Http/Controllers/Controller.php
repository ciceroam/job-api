<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Server(url="http://localhost:8000/api"),
 * @OA\Info(title="Job API Documentation", version="1.0.0")
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
