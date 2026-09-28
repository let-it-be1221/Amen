<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@amen.com',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'phone' => '+251 911 000 001',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create supervisor
        $supervisor = User::create([
            'name' => 'Sarah Supervisor',
            'email' => 'supervisor@amen.com',
            'password' => 'password',
            'role' => User::ROLE_SUPERVISOR,
            'phone' => '+251 911 000 002',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create waiters
        $waiters = collect([
            ['name' => 'John Waiter', 'email' => 'waiter@amen.com'],
            ['name' => 'Maria Garcia', 'email' => 'maria@amen.com'],
            ['name' => 'David Smith', 'email' => 'david@amen.com'],
        ])->map(function ($data) {
            return User::create(array_merge($data, [
                'password' => 'password',
                'role' => User::ROLE_WAITER,
                'phone' => '+251 911 000 0' . rand(10, 99),
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        });

        // Create cookers
        $cookers = collect([
            ['name' => 'Chef Antonio', 'email' => 'cooker@amen.com'],
            ['name' => 'Chef Linda', 'email' => 'linda@amen.com'],
        ])->map(function ($data) {
            return User::create(array_merge($data, [
                'password' => 'password',
                'role' => User::ROLE_COOKER,
                'phone' => '+251 911 000 0' . rand(10, 99),
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        });

        // Create cashier
        $cashier = User::create([
            'name' => 'Carol Cashier',
            'email' => 'cashier@amen.com',
            'password' => 'password',
            'role' => User::ROLE_CASHIER,
            'phone' => '+251 911 000 003',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Appetizers', 'description' => 'Start your meal with these delicious bites', 'sort_order' => 1, 'icon' => 'fire'],
            ['name' => 'Main Courses', 'description' => 'Hearty dishes for the main course', 'sort_order' => 2, 'icon' => 'menu'],
            ['name' => 'Pasta & Pizza', 'description' => 'Italian classics made fresh', 'sort_order' => 3, 'icon' => 'menu'],
            ['name' => 'Salads', 'description' => 'Fresh and healthy salads', 'sort_order' => 4, 'icon' => 'menu'],
            ['name' => 'Desserts', 'description' => 'Sweet endings to your meal', 'sort_order' => 5, 'icon' => 'menu'],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks', 'sort_order' => 6, 'icon' => 'menu'],
        ];

        $categoryModels = collect($categories)->map(fn($c) => Category::create($c));

        // Create menu items
        $menuItems = [
            // Appetizers
            ['category' => 'Appetizers', 'name' => 'Bruschetta', 'description' => 'Grilled bread with tomatoes, garlic, and basil', 'price' => 7.50, 'preparation_time' => 10, 'is_vegetarian' => true],
            ['category' => 'Appetizers', 'name' => 'Mozzarella Sticks', 'description' => 'Golden fried mozzarella with marinara sauce', 'price' => 8.00, 'preparation_time' => 12, 'is_vegetarian' => true],
            ['category' => 'Appetizers', 'name' => 'Buffalo Wings', 'description' => 'Spicy chicken wings with blue cheese dip', 'price' => 10.50, 'preparation_time' => 15, 'is_spicy' => true],
            ['category' => 'Appetizers', 'name' => 'Calamari Fritti', 'description' => 'Crispy fried squid with lemon and tartar sauce', 'price' => 11.00, 'preparation_time' => 15],

            // Main Courses
            ['category' => 'Main Courses', 'name' => 'Grilled Salmon', 'description' => 'Atlantic salmon with lemon butter sauce and vegetables', 'price' => 24.00, 'preparation_time' => 25],
            ['category' => 'Main Courses', 'name' => 'Ribeye Steak', 'description' => '12oz ribeye steak cooked to order with mashed potatoes', 'price' => 32.00, 'preparation_time' => 30],
            ['category' => 'Main Courses', 'name' => 'Chicken Parmesan', 'description' => 'Breaded chicken with marinara and melted mozzarella', 'price' => 18.50, 'preparation_time' => 25],
            ['category' => 'Main Courses', 'name' => 'Lamb Chops', 'description' => 'Grilled lamb chops with rosemary and red wine reduction', 'price' => 28.00, 'preparation_time' => 30],
            ['category' => 'Main Courses', 'name' => 'Vegetable Stir-Fry', 'description' => 'Mixed vegetables in a savory sauce over rice', 'price' => 14.00, 'preparation_time' => 15, 'is_vegetarian' => true],

            // Pasta & Pizza
            ['category' => 'Pasta & Pizza', 'name' => 'Margherita Pizza', 'description' => 'Classic pizza with tomato, mozzarella, and basil', 'price' => 14.00, 'preparation_time' => 20, 'is_vegetarian' => true],
            ['category' => 'Pasta & Pizza', 'name' => 'Spaghetti Carbonara', 'description' => 'Pasta with bacon, eggs, and parmesan', 'price' => 16.00, 'preparation_time' => 18],
            ['category' => 'Pasta & Pizza', 'name' => 'Pepperoni Pizza', 'description' => 'Pizza topped with pepperoni and mozzarella', 'price' => 16.50, 'preparation_time' => 20],
            ['category' => 'Pasta & Pizza', 'name' => 'Fettuccine Alfredo', 'description' => 'Creamy alfredo sauce over fettuccine', 'price' => 15.00, 'preparation_time' => 18, 'is_vegetarian' => true],

            // Salads
            ['category' => 'Salads', 'name' => 'Caesar Salad', 'description' => 'Romaine lettuce with caesar dressing and croutons', 'price' => 9.00, 'preparation_time' => 8, 'is_vegetarian' => true],
            ['category' => 'Salads', 'name' => 'Greek Salad', 'description' => 'Tomatoes, cucumbers, olives, and feta cheese', 'price' => 10.00, 'preparation_time' => 8, 'is_vegetarian' => true],
            ['category' => 'Salads', 'name' => 'Garden Salad', 'description' => 'Mixed greens with seasonal vegetables', 'price' => 8.00, 'preparation_time' => 5, 'is_vegetarian' => true],

            // Desserts
            ['category' => 'Desserts', 'name' => 'Tiramisu', 'description' => 'Classic Italian coffee-flavored dessert', 'price' => 7.50, 'preparation_time' => 5, 'is_vegetarian' => true],
            ['category' => 'Desserts', 'name' => 'Cheesecake', 'description' => 'New York style cheesecake with berry sauce', 'price' => 8.00, 'preparation_time' => 5, 'is_vegetarian' => true],
            ['category' => 'Desserts', 'name' => 'Chocolate Lava Cake', 'description' => 'Warm chocolate cake with a molten center', 'price' => 9.00, 'preparation_time' => 12, 'is_vegetarian' => true],

            // Beverages
            ['category' => 'Beverages', 'name' => 'Fresh Orange Juice', 'description' => 'Freshly squeezed oranges', 'price' => 4.50, 'preparation_time' => 3, 'is_vegetarian' => true],
            ['category' => 'Beverages', 'name' => 'Iced Coffee', 'description' => 'Chilled coffee with milk and ice', 'price' => 3.50, 'preparation_time' => 3, 'is_vegetarian' => true],
            ['category' => 'Beverages', 'name' => 'Sparkling Water', 'description' => '500ml sparkling mineral water', 'price' => 2.50, 'preparation_time' => 1, 'is_vegetarian' => true],
            ['category' => 'Beverages', 'name' => 'Lemonade', 'description' => 'Fresh squeezed lemonade', 'price' => 3.50, 'preparation_time' => 3, 'is_vegetarian' => true],
        ];

        foreach ($menuItems as $item) {
            $categoryName = $item['category'];
            unset($item['category']);
            $category = $categoryModels->firstWhere('name', $categoryName);
            $item['category_id'] = $category->id;
            MenuItem::create($item);
        }

        // Create tables
        $tables = [
            ['name' => 'T-01', 'seats' => 2, 'location' => 'Window'],
            ['name' => 'T-02', 'seats' => 4, 'location' => 'Indoor'],
            ['name' => 'T-03', 'seats' => 4, 'location' => 'Indoor'],
            ['name' => 'T-04', 'seats' => 6, 'location' => 'Indoor'],
            ['name' => 'T-05', 'seats' => 8, 'location' => 'VIP'],
            ['name' => 'T-06', 'seats' => 2, 'location' => 'Outdoor'],
            ['name' => 'T-07', 'seats' => 4, 'location' => 'Outdoor'],
            ['name' => 'T-08', 'seats' => 10, 'location' => 'VIP'],
        ];
        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }

        // System settings
        SystemSetting::set('tax_rate', '10', 'tax');
        SystemSetting::set('service_charge_rate', '5', 'tax');
        SystemSetting::set('restaurant_name', 'Amen Restaurant', 'general');
        SystemSetting::set('restaurant_phone', '+251 11 234 5678', 'general');
        SystemSetting::set('restaurant_address', 'Bole Road, Addis Ababa, Ethiopia', 'general');
        SystemSetting::set('currency', 'USD', 'general');
        SystemSetting::set('currency_symbol', '$', 'general');
    }
}
