<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiDocumentationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'name' => 'Laravel POS SaaS API',
            'version' => '1.0.0',
            'description' => 'Multi-tenant POS system API',
            'endpoints' => [
                'auth' => [
                    'POST /api/login' => 'User authentication',
                    'POST /api/logout' => 'User logout',
                    'POST /api/register' => 'User registration'
                ],
                'pos' => [
                    'GET /api/pos/products' => 'Get products for POS',
                    'POST /api/pos/checkout' => 'Process POS checkout',
                    'GET /api/pos/customers' => 'Get customers'
                ],
                'health' => [
                    'GET /api/health' => 'System health check'
                ]
            ],
            'authentication' => 'Bearer token required for protected endpoints',
            'rate_limiting' => '60 requests per minute per IP'
        ]);
    }
}