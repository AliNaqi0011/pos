<?php
// Run this file once to setup database (DELETE after use)

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Run migrations
    echo "Running migrations...\n";
    $kernel->call('migrate', ['--force' => true]);
    echo "Migrations completed!\n";
    
    // Run seeders
    echo "Running seeders...\n";
    $kernel->call('db:seed', ['--force' => true]);
    echo "Seeders completed!\n";
    
    // Clear cache
    echo "Clearing cache...\n";
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('view:clear');
    echo "Cache cleared!\n";
    
    echo "\n=== SETUP COMPLETE ===\n";
    echo "Default Login Credentials:\n";
    echo "Super Admin: superadmin@example.com / password\n";
    echo "Admin: admin@example.com / password\n";
    echo "Seller: seller@example.com / password\n";
    echo "\n⚠️  DELETE THIS FILE FOR SECURITY!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your .env database settings.\n";
}
?>