@echo off
echo Running comprehensive controller tests...
echo.

echo Testing POS Controller...
php artisan test --filter=test_pos_controller_index
php artisan test --filter=test_pos_checkout_functionality

echo.
echo Testing Product Controller...
php artisan test --filter=test_product_controller_crud

echo.
echo Testing Sale Controller...
php artisan test --filter=test_sale_controller_crud

echo.
echo Testing Customer Controller...
php artisan test --filter=test_customer_controller_crud

echo.
echo Testing Category Controller...
php artisan test --filter=test_category_controller_crud

echo.
echo Testing Brand Controller...
php artisan test --filter=test_brand_controller_crud

echo.
echo Testing Warehouse Controller...
php artisan test --filter=test_warehouse_controller_crud

echo.
echo Testing Expense Controller...
php artisan test --filter=test_expense_controller_crud

echo.
echo Testing User Controller...
php artisan test --filter=test_user_permissions
php artisan test --filter=test_data_isolation

echo.
echo Testing Validation...
php artisan test --filter=test_validation_rules

echo.
echo Testing Stock Management...
php artisan test --filter=test_stock_management

echo.
echo Running all comprehensive tests...
php artisan test tests/Feature/ComprehensiveControllerTest.php

echo.
echo Test run completed!
pause