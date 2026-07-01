<?php
declare(strict_types=1);

namespace App\Controllers;

class HealthController extends Controller
{
    public function index(array $params = []): void
    {
        $this->jsonResponse([
            'status' => 'ok',
            'message' => 'API is running',
        ]);
    }
}
