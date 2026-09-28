# 🍽️ Amen Restaurant Management System

A complete restaurant management system built with **Laravel 12**, **MySQL**, and a modern Tailwind CSS UI. Designed to track orders through the full kitchen workflow: **Customer → Waiter → Cooker → Delivered → Cashier → Payment**.

## ✨ Features

### Five User Roles

| Role | Capabilities |
|------|-------------|
| **Waiter** | Create new orders (POS-style menu), submit to kitchen, view own orders & status, mark orders as delivered, reassign orders to other waiters, view customer menu QR code |
| **Cooker** | Accept incoming orders, start cooking, mark orders as ready (auto-notifies the waiter), view preparation history |
| **Supervisor** | Monitor all orders, filter by waiter/cooker/date/status, generate daily/weekly/monthly reports, **manage menu items and categories**, view top performers |
| **Cashier** | View delivered orders awaiting payment, process payments (cash/card/mobile), print receipts, view transaction history |
| **Admin** | Full user management (CRUD + activate/deactivate), menu categories, menu items, restaurant tables, system settings |

### Order Workflow

```
Ordered → Accepted → Cooking → Ready → Delivered → Paid
   ↓         ↓         ↓         ↓         ↓
   └─────────┴─────────┴─────────┴──→ Cancelled (terminal)
```

Each transition is recorded in `order_status_histories` with the responsible user and timestamp, providing a complete audit trail.

### 🆕 Recently Added Features

1. **🔔 Order Ready Notifications** — When a cooker marks an order Ready, the waiter who took the order receives a database notification. A bell icon in the topbar shows the unread count and polls every 20 seconds. Clicking a notification marks it read and jumps to the order detail page.

2. **🔄 Order Reassignment** — End-of-shift handover. The waiter who owns an order can hand it off to another active waiter, with a reason recorded in the audit trail. The new waiter takes over delivery and tracking responsibility.

3. **👨‍🍳 Supervisor Menu Management** — Supervisors can now add/edit/remove menu items and categories (admin retains the same access). User management and tables remain admin-only for proper privilege separation.

4. **📱 Customer Menu QR Code** — Each waiter's dashboard shows a QR code that customers can scan to view the live menu on their phone (no app needed). The QR code automatically regenerates whenever a supervisor or admin adds, edits, removes, or toggles the availability of a menu item.

### Key Capabilities

- **Order history tracking**: Every order shows who took it (waiter), who cooked it (cooker), and who processed the payment (cashier).
- **Payment safety**: Only delivered orders are eligible for payment. Cancelled, uncooked, or undelivered orders are excluded from the customer's payable amount.
- **Snapshot pricing**: Order items store the price at order time, so menu price changes don't affect existing orders.
- **Role-based dashboard**: Each role sees a customized dashboard with relevant stats and quick actions.
- **Public customer menu**: Beautiful mobile-friendly menu page accessible via QR code or direct link — no auth required.
- **Responsive design**: Works beautifully on desktop, tablet, and mobile.

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL / MariaDB (via XAMPP)
- **Frontend**: Tailwind CSS, Alpine.js, Vite
- **Auth**: Laravel Breeze (Blade)
- **Notifications**: Laravel Database Notifications
- **QR Codes**: api.qrserver.com (no API key required)
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

### 8. Create the storage symlink (for menu item images)

```bash
php artisan storage:link
```

### 9. Build front-end assets

For development (with hot reload):

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 10. Start the server

```bash
php artisan serve
```

Visit **http://localhost:8000** in your browser.

## 🔑 Demo Accounts

After seeding, you can sign in with any of these accounts (password for all: `password`):

| Role | Email | Capabilities |
|------|-------|-------------|
| Admin | `admin@amen.com` | Full system access |
| Supervisor | `supervisor@amen.com` | Reports, monitoring, menu management |
| Waiter | `waiter@amen.com` | Take orders, deliver, reassign, view menu QR |
| Cooker | `cooker@amen.com` | Kitchen, mark orders ready |
| Cashier | `cashier@amen.com` | Process payments |

You can also use the quick-login buttons on the login page to auto-fill credentials.

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # Admin role controllers (users, tables, menu, categories)
│   │   ├── Cashier/       # Cashier role controllers
│   │   ├── Cooker/        # Cooker role controllers
│   │   ├── Supervisor/    # Supervisor role controllers
│   │   ├── Waiter/        # Waiter role controllers
│   │   ├── DashboardController.php  # Role-based dashboard router
│   │   ├── MenuController.php       # Public customer menu + QR code endpoint
│   │   └── NotificationController.php  # AJAX-polled notifications
│   └── Middleware/
│       └── RoleMiddleware.php  # Role-based access control
├── Models/                # Eloquent models with relationships
├── Notifications/
│   └── OrderReady.php    # Database notification sent to waiter when order is ready
├── Policies/
│   └── OrderPolicy.php    # Authorization rules (view, cancel, deliver, reassign)
└── Services/
    └── OrderWorkflowService.php  # Order status transition logic

database/
├── migrations/            # 11 migrations including notifications table
└── seeders/
    └── DatabaseSeeder.php # Demo data seeder

resources/views/
├── layouts/               # App layout, sidebar, topbar (with notification bell)
├── components/            # Reusable Blade components
├── menu/public.blade.php  # Customer-facing public menu page (no auth)
├── admin/                 # Admin role views
├── supervisor/            # Supervisor role views
├── waiter/                # Waiter role views (dashboard has QR code card)
├── cooker/                # Cooker role views
└── cashier/               # Cashier role views
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
- **order_status_histories** - Audit trail of every status change (including reassignments)
- **notifications** - Laravel's built-in notifications table (for OrderReady alerts)
- **system_settings** - Key-value system configuration (tax rate, service charge, menu_version, etc.)
- **activity_logs** - Generic activity logging

## 🔄 Order Workflow Detail

### Status Flow

1. **Ordered** - Waiter creates the order
2. **Accepted** - Cooker accepts the order (assigns themselves)
3. **Cooking** - Cooker starts cooking
4. **Ready** - Cooker marks order as ready for delivery → **waiter receives notification**
5. **Delivered** - Waiter delivers to customer and confirms
6. **Paid** - Cashier processes payment

### Cancelled State

Orders can be cancelled from any pre-payment state. Cancelled orders are excluded from payment totals.

### Reassignment

The waiter who owns an active order can hand it off to another waiter (e.g., end-of-shift). The handover is recorded in the audit trail with the reason.

### Payment Rules

- Only orders in **Delivered** status can be paid
- Tax rate and service charge are configurable via system settings
- Discounts can be applied per payment
- Change is calculated automatically
- Receipts are printable

## 📱 Customer Menu QR Code

Each waiter's dashboard displays a QR code that points to `/menu` — a public, no-auth-required page showing the live menu. The QR code includes a version hash that changes whenever the menu is modified:

- Supervisor or admin **adds** a menu item → version bumps → QR code regenerates
- Supervisor or admin **edits** a menu item → version bumps → QR code regenerates
- Supervisor or admin **deletes** a menu item → version bumps → QR code regenerates
- Supervisor or admin **toggles availability** → version bumps → QR code regenerates

The waiter's dashboard polls the `/menu/qrcode` endpoint every 30 seconds and automatically refreshes the QR image when the version changes. No manual reload needed.

## 🎨 UI/UX Features

- **Modern dark sidebar** with role-based menu items
- **Sticky topbar** with user info, date/time, and notification bell (for waiters)
- **Color-coded status badges** throughout the interface
- **Order progress timeline** showing every stage
- **POS-style menu grid** for waiters (with cart sidebar)
- **Kitchen display** for cookers with incoming/active/ready columns
- **Receipt view** for cashiers with print support
- **Reports dashboard** for supervisors with revenue trends and top performers
- **QR code card** on waiter dashboard with preview, copy, download, and print buttons
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

### Restaurant Information (shown on customer menu)

```bash
php artisan tinker
>>> App\Models\SystemSetting::set('restaurant_name', 'Your Restaurant Name');
>>> App\Models\SystemSetting::set('restaurant_phone', '+251 11 234 5678');
>>> App\Models\SystemSetting::set('restaurant_address', 'Your Address Here');
>>> App\Models\SystemSetting::set('currency_symbol', '$');
```

### Adding Menu Item Images

When creating/editing menu items in the admin or supervisor panel, you can upload images. They're stored in `storage/app/public/menu-items/`. Make sure you've run:

```bash
php artisan storage:link
```

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

### Notification bell doesn't show

The bell only appears for users with the **waiter** role. Make sure you're signed in as a waiter (e.g., `waiter@amen.com`).

### QR code doesn't refresh

The waiter dashboard polls every 30 seconds. To force an immediate refresh, reload the page. If the menu was just changed by a supervisor, the QR code will update on the next poll cycle.

## 📄 License

This project is proprietary. All rights reserved.

## 🤝 Support

For questions or issues, please contact the system administrator.
