<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecurityException extends Exception
{
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Security violation detected',
                'message' => 'Your request has been blocked for security reasons'
            ], 403);
        }

        return response()->view('errors.security', [], 403);
    }
}