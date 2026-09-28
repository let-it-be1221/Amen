<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Public-facing menu page - shown when a customer scans the QR code.
     * No authentication required. Shows only currently-available menu items.
     */
    public function publicIndex(Request $request): View
    {
        $restaurantName = SystemSetting::get('restaurant_name', config('app.name', 'Amen Restaurant'));
        $restaurantPhone = SystemSetting::get('restaurant_phone');
        $restaurantAddress = SystemSetting::get('restaurant_address');
        $currencySymbol = SystemSetting::get('currency_symbol', '$');

        $categories = Category::where('is_active', true)
            ->whereHas('menuItems', function ($q) {
                $q->where('is_available', true);
            })
            ->with(['menuItems' => function ($q) {
                $q->where('is_available', true)->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $menuVersion = SystemSetting::get('menu_version', 'initial');

        return view('menu.public', compact(
            'categories',
            'restaurantName',
            'restaurantPhone',
            'restaurantAddress',
            'currencySymbol',
            'menuVersion',
        ));
    }

    /**
     * Return the QR code URL pointing to the public menu page.
     * Polled via AJAX by the waiter dashboard so the QR code refreshes
     * when the supervisor/admin adds or removes a menu item.
     */
    public function qrCode(Request $request)
    {
        $version = SystemSetting::get('menu_version', 'initial');

        // Build the full URL to the public menu page (with version parameter
        // so the QR code visually changes when the menu changes)
        $menuUrl = rtrim($request->root(), '/') . '/menu?v=' . substr(md5($version), 0, 8);

        // Use the public qrserver.com API to render the QR code image
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=10&data=' . urlencode($menuUrl);

        if ($request->wantsJson()) {
            return response()->json([
                'qr_url' => $qrImageUrl,
                'menu_url' => $menuUrl,
                'version' => $version,
                'version_short' => substr(md5($version), 0, 8),
                'updated_at' => now()->toIso8601String(),
            ]);
        }

        return response()->stream(function () use ($qrImageUrl) {
            // Fetch the image bytes and stream them through
            $ch = curl_init($qrImageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
            ]);
            $image = curl_exec($ch);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/png';
            curl_close($ch);
            header('Content-Type: ' . $contentType);
            header('Cache-Control: no-cache, no-store, must-revalidate');
            echo $image;
        }, 200, ['Content-Type' => 'image/png']);
    }
}
