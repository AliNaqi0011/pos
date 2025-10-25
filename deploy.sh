#!/bin/bash

# Laravel POS Production Deployment Script

set -e

echo "🚀 Starting Laravel POS deployment..."

# Check if we're in production
if [ "$APP_ENV" != "production" ]; then
    echo "❌ This script should only run in production environment"
    exit 1
fi

# Backup database before deployment
echo "📦 Creating database backup..."
php artisan backup:database

# Put application in maintenance mode
echo "🔧 Enabling maintenance mode..."
php artisan down --retry=60

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm ci --production

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear application cache
php artisan cache:clear
php artisan queue:restart

# Build assets
echo "🎨 Building assets..."
npm run build

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Bring application back online
echo "✅ Disabling maintenance mode..."
php artisan up

# Run health check
echo "🏥 Running health check..."
php artisan health:check

echo "🎉 Deployment completed successfully!"