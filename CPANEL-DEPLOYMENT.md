# cPanel Deployment Guide

## Step 1: Prepare Files
1. **Delete these folders/files before upload:**
   - `node_modules/`
   - `.git/`
   - `tests/`
   - `docker/`
   - `docker-compose.yml`
   - `Dockerfile`
   - `railway.toml`
   - `nixpacks.toml`

## Step 2: Upload to cPanel
1. Compress entire project folder
2. Upload to cPanel File Manager
3. Extract in a folder (e.g., `laravel-app`)
4. Copy `cpanel-index.php` to `public_html/index.php`
5. Edit the path in `public_html/index.php` to match your folder name

## Step 3: Database Setup
1. Create MySQL database in cPanel
2. Create database user and assign to database
3. Update `.env` file with database credentials:
   ```
   DB_HOST=localhost
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

## Step 4: Run Setup
1. Visit `yourdomain.com/laravel-app/cpanel-setup.php`
2. Visit `yourdomain.com/laravel-app/run-migrations.php`
3. **DELETE both setup files after successful run**

## Step 5: Security
1. Set folder permissions:
   - `storage/` → 755
   - `bootstrap/cache/` → 755
2. Delete setup files
3. Update APP_URL in .env to your domain

## Default Login:
- **Super Admin:** superadmin@example.com / password
- **Admin:** admin@example.com / password  
- **Seller:** seller@example.com / password

## Troubleshooting:
- If 500 error: Check error logs in cPanel
- If database error: Verify credentials in .env
- If permission error: Set storage folders to 755