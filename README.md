# 🍽️ Amen Restaurant Management System

A complete restaurant management system built with **Laravel 12**, **MySQL**, and a modern Tailwind CSS UI. Designed to track orders through the full kitchen workflow: **Customer → Waiter → Cooker → Delivered → Cashier → Payment**.

## ✨ Features

### Five User Roles

| Role | Capabilities |
|------|-------------|
| **Waiter** | Create new orders (POS-style menu), submit to kitchen, view own orders & status, cancel pending orders |
| **Cooker** | Accept incoming orders, start cooking, mark orders as ready, view preparation history |
| **Supervisor** | Monitor all orders, filter by waiter/cooker/date/status, generate daily/weekly/monthly reports, view top performers |
| **Cashier** | View delivered orders awaiting payment, process payments (cash/card/mobile), print receipts, view transaction history |
| **Admin** | Full user management (CRUD + activate/deactivate), menu categories, menu items, restaurant tables, system settings |

### Order Workflow

```
Ordered → Accepted → Cooking → Ready → Delivered → Paid
   ↓         ↓         ↓         ↓         ↓
   └─────────┴─────────┴─────────┴──→ Cancelled (terminal)
```

Each transition is recorded in `order_status_histories` with the responsible user and timestamp, providing a complete audit trail.

### Key Capabilities

- **Order history tracking**: Every order shows who took it (waiter), who cooked it (cooker), and who processed the payment (cashier).
- **Payment safety**: Only delivered orders are eligible for payment. Cancelled, uncooked, or undelivered orders are excluded from the customer's payable amount.
- **Snapshot pricing**: Order items store the price at order time, so menu price changes don't affect existing orders.
- **Role-based dashboard**: Each role sees a customized dashboard with relevant stats and quick actions.
- **Responsive design**: Works beautifully on desktop, tablet, and mobile.

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL / MariaDB (via XAMPP)
- **Frontend**: Tailwind CSS, Alpine.js, Vite
- **Auth**: Laravel Breeze (Blade)
- **Icons**: Heroicons (inline SVG)

## 📋 Requirements

- PHP 8.2 or higher
- Composer 2.x
- MySQL 5.7+ or MariaDB 10.4+
- Node.js 18+ and npm
- XAMPP (recommended) or any local PHP/MySQL stack

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/let-it-be1221/Amen.git
cd Amen
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install front-end dependencies

```bash
npm install
```

### 4. Configure environment

Copy the example env file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 5. Set up your MySQL database

Open **phpMyAdmin** (http://localhost/phpmyadmin) or your MySQL client and create a new database:

```sql
CREATE DATABASE amen_restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amen_restaurant
DB_USERNAME=root
DB_PASSWORD=
```

> **XAMPP default**: username `root`, password empty.

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Seed the database (optional but recommended)

This creates demo users, sample menu categories/items, and restaurant tables:

```bash
php artisan db:seed
```

### 8. Build front-end assets

For development (with hot reload):

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 9. Start the server

```bash
php artisan serve
```

Visit **http://localhost:8000** in your browser.

## 🔑 Demo Accounts

After seeding, you can sign in with any of these accounts (password for all: `password`):

| Role | Email | Capabilities |
|------|-------|-------------|
| Admin | `admin@amen.com` | Full system access |
| Supervisor | `supervisor@amen.com` | Reports, monitoring |
| Waiter | `waiter@amen.com` | Take orders |
| Cooker | `cooker@amen.com` | Kitchen management |
| Cashier | `cashier@amen.com` | Process payments |

You can also use the quick-login buttons on the login page to auto-fill credentials.

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # Admin role controllers
│   │   ├── Cashier/       # Cashier role controllers
│   │   ├── Cooker/        # Cooker role controllers
│   │   ├── Supervisor/    # Supervisor role controllers
│   │   ├── Waiter/        # Waiter role controllers
│   │   └── DashboardController.php  # Role-based dashboard router
│   └── Middleware/
│       └── RoleMiddleware.php  # Role-based access control
├── Models/                # Eloquent models with relationships
├── Policies/
│   └── OrderPolicy.php    # Authorization rules per role
└── Services/
    └── OrderWorkflowService.php  # Order status transition logic

database/
├── migrations/            # 10 migrations defining the schema
└── seeders/
    └── DatabaseSeeder.php # Demo data seeder

resources/views/
├── layouts/               # App layout, sidebar, topbar
├── components/             # Reusable Blade components
├── admin/                  # Admin role views
├── supervisor/             # Supervisor role views
├── waiter/                  # Waiter role views
├── cooker/                  # Cooker role views
└── cashier/                 # Cashier role views
```

## 🗄️ Database Schema

### Core Tables

- **users** - System users with role field (admin/supervisor/waiter/cooker/cashier)
- **categories** - Menu categories (Appetizers, Main Courses, etc.)
- **menu_items** - Individual menu items with price, availability, dietary flags
- **tables** - Restaurant tables with capacity and status
- **orders** - Main order record linking waiter, cooker, cashier, table
- **order_items** - Individual items in an order (with price snapshot)
- **payments** - Payment records (cash/card/mobile, amount paid, change)
- **order_status_histories** - Audit trail of every status change
- **system_settings** - Key-value system configuration
- **activity_logs** - Generic activity logging

## 🔄 Order Workflow Detail

### Status Flow

1. **Ordered** - Waiter creates the order
2. **Accepted** - Cooker accepts the order (assigns themselves)
3. **Cooking** - Cooker starts cooking
4. **Ready** - Cooker marks order as ready for delivery
5. **Delivered** - Waiter marks order as delivered to customer
6. **Paid** - Cashier processes payment

### Cancelled State

Orders can be cancelled from any pre-payment state. Cancelled orders are excluded from payment totals.

### Payment Rules

- Only orders in **Delivered** status can be paid
- Tax rate and service charge are configurable via system settings
- Discounts can be applied per payment
- Change is calculated automatically
- Receipts are printable

## 🎨 UI/UX Features

- **Modern dark sidebar** with role-based menu items
- **Sticky topbar** with user info and date/time
- **Color-coded status badges** throughout the interface
- **Order progress timeline** showing every stage
- **POS-style menu grid** for waiters (with cart sidebar)
- **Kitchen display** for cookers with incoming/active/ready columns
- **Receipt view** for cashiers with print support
- **Reports dashboard** for supervisors with revenue trends and top performers
- **Responsive design** - works on mobile, tablet, and desktop
- **Flash messages** for success/error feedback
- **Empty states** with helpful illustrations and CTAs

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 📝 Configuration

### Tax and Service Charge

After installation, set your tax rate and service charge via tinker:

```bash
php artisan tinker
>>> App\Models\SystemSetting::set('tax_rate', '10');  // 10% tax
>>> App\Models\SystemSetting::set('service_charge_rate', '5');  // 5% service charge
```

### Adding Menu Item Images

When creating/editing menu items in the admin panel, you can upload images. They're stored in `storage/app/public/menu-items/`. Make sure to run:

```bash
php artisan storage:link
```

This creates a symlink from `public/storage` to `storage/app/public`.

## 🚨 Troubleshooting

### Migration errors

```bash
php artisan migrate:fresh --seed
```

This drops all tables and re-runs migrations + seeders.

### Permission denied on storage

```bash
chmod -R 775 storage bootstrap/cache
```

### CSS/JS not loading

Make sure you've built the assets:

```bash
npm run build
```

### 419 Page Expired

This is a CSRF token issue. Clear your browser cookies for the site and try again.

### Can't sign in

Make sure you've run the seeder:

```bash
php artisan db:seed
```

## 📄 License

This project is proprietary. All rights reserved.

## 🤝 Support

For questions or issues, please contact the system administrator.
