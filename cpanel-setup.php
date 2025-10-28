<?php
// cPanel Setup Script - Run this once after upload

// Set proper permissions
chmod(__DIR__ . '/storage', 0755);
chmod(__DIR__ . '/storage/logs', 0755);
chmod(__DIR__ . '/storage/framework', 0755);
chmod(__DIR__ . '/storage/framework/cache', 0755);
chmod(__DIR__ . '/storage/framework/sessions', 0755);
chmod(__DIR__ . '/storage/framework/views', 0755);
chmod(__DIR__ . '/bootstrap/cache', 0755);

// Create necessary directories if they don't exist
$directories = [
    'storage/logs',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/app/public',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        mkdir(__DIR__ . '/' . $dir, 0755, true);
    }
}

// Create .env file from production template
if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/.env.production')) {
    copy(__DIR__ . '/.env.production', __DIR__ . '/.env');
}

echo "Setup completed! Please:\n";
echo "1. Update .env file with your database credentials\n";
echo "2. Run database migrations through cPanel PHP selector or file manager\n";
echo "3. Delete this setup file for security\n";
?>