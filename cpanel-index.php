<?php

// cPanel Root Index File
// Copy this to your domain root (public_html)

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/laravel-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/laravel-app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/laravel-app/bootstrap/app.php')
    ->handleRequest(Request::capture());