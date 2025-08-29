# Laravel POS SaaS System

A comprehensive multi-tenant Point of Sale (POS) system built with Laravel, featuring subscription management, role-based access control, and FBR integration for Pakistan.

## Features

### 🏢 Multi-Tenant SaaS Architecture
- **Data Isolation**: Each tenant's data is completely isolated
- **Subscription Management**: Stripe integration for recurring payments
- **Role-Based Access**: 5 distinct roles (Super Admin, Admin, Seller, Manager, Sales)
- **Tenant Management**: Super admin can manage all tenants

### 💼 Business Management
- **Inventory Management**: Products, categories, brands, warehouses
- **Sales Management**: POS system, quotations, invoices
- **Purchase Management**: Purchase orders, returns, payments
- **Customer Management**: Customer database and history
- **Reporting**: Comprehensive sales, product, and customer reports

### 🇵🇰 Pakistan-Specific Features
- **FBR Integration**: Automatic invoice submission to Federal Board of Revenue
- **Tax Compliance**: Built-in tax calculations and reporting
- **Local Currency**: PKR support with proper formatting

### 🎨 Customization
- **Project Settings**: Each admin can customize their dashboard
- **Branding**: Custom logos, company information
- **Localization**: Multiple currencies, timezones, date formats

## Quick Start with Docker

### Prerequisites
- Docker
- Docker Compose

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/AliNaqi0011/pos.git
   cd pos
   ```

2. **Copy environment file**
   ```bash
   cp .env.docker .env
   ```

3. **Generate application key**
   ```bash
   docker-compose run --rm app php artisan key:generate
   ```

4. **Start the application**
   ```bash
   docker-compose up -d
   ```

5. **Run migrations and seeders**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install && npm run build
   ```

### Access the Application

- **Application**: http://localhost:8000
- **Database**: localhost:3307 (MySQL)
- **Redis**: localhost:6380

### Default Login Credentials

- **Super Admin**: superadmin@example.com / password
- **Admin**: admin@example.com / password
- **Seller**: seller@example.com / password

## Manual Installation

### Requirements
- PHP 8.2+
- MySQL 8.0+
- Redis
- Composer
- Node.js & NPM

### Steps

1. **Clone and install dependencies**
   ```bash
   git clone https://github.com/AliNaqi0011/pos.git
   cd pos
   composer install
   npm install && npm run build
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
   ```bash
   php artisan migrate --seed
   ```

4. **Start the application**
   ```bash
   php artisan serve
   ```

## System Architecture

### Role Hierarchy
```
Super Admin
├── Tenant Management
├── Subscription Management
├── Global Settings
└── All System Access

Admin (Per Tenant)
├── User Management
├── Business Operations
├── Project Settings
└── FBR Integration

Seller/Manager/Sales
├── POS Operations
├── Inventory Management
├── Customer Management
└── Reporting (Limited)
```

### Data Flow
```
Registration → Plan Selection → Payment → Approval → Tenant Creation → Customization → Usage
```

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout

### POS Operations
- `GET /pos` - POS interface
- `POST /pos/checkout` - Process sale
- `GET /pos/products` - Get products

### Management
- `GET /admin/sales` - Sales management
- `GET /admin/products` - Product management
- `GET /admin/customers` - Customer management

## Configuration

### FBR Integration
1. Go to Project Settings
2. Enable FBR Integration
3. Enter your FBR credentials:
   - POS ID
   - Username
   - Password
   - API URL (default: https://esp.fbr.gov.pk)
4. Test connection

### Subscription Plans
Configure in Super Admin panel:
- Plan names and features
- Pricing (monthly/yearly)
- User limits
- Feature restrictions

## Docker Services

- **app**: Laravel application (PHP 8.2-FPM)
- **webserver**: Nginx web server
- **db**: MySQL 8.0 database
- **redis**: Redis cache and sessions
- **queue**: Laravel queue worker
- **scheduler**: Laravel task scheduler

## Development

### Running Tests
```bash
docker-compose exec app php artisan test
```

### Queue Processing
```bash
docker-compose exec app php artisan queue:work
```

### Database Seeding
```bash
docker-compose exec app php artisan db:seed
```

## Production Deployment

1. **Environment Configuration**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure proper database credentials
   - Set up SSL certificates

2. **Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Queue Workers**
   - Set up supervisor for queue workers
   - Configure cron for scheduler

## Support

For support and questions:
- GitHub Issues: [Create an issue](https://github.com/AliNaqi0011/pos/issues)
- Email: support@yourcompany.com

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

**Built with ❤️ for the Pakistani business community**