@echo off
echo ========================================
echo    Laravel POS System - Test Suite
echo ========================================
echo.

echo [1/4] Setting up test environment...
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo.
echo [2/4] Running Feature Tests...
echo ========================================
vendor\bin\phpunit tests\Feature --verbose --colors=always

echo.
echo [3/4] Running Unit Tests...
echo ========================================
vendor\bin\phpunit tests\Unit --verbose --colors=always

echo.
echo [4/4] Running All Tests with Coverage...
echo ========================================
vendor\bin\phpunit --coverage-text --colors=always

echo.
echo ========================================
echo    Test Suite Complete!
echo ========================================
pause