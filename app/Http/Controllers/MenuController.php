<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\SystemSetting;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
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

        $versionShort = substr(md5($version), 0, 8);
        $menuUrl = rtrim($request->root(), '/') . '/menu?v=' . $versionShort;
        $qrImageUrl = route('menu.qrcode', ['v' => $versionShort]);

        if ($request->wantsJson()) {
            return response()->json([
                'qr_url' => $qrImageUrl,
                'menu_url' => $menuUrl,
                'version' => $version,
                'version_short' => $versionShort,
                'updated_at' => now()->toIso8601String(),
            ]);
        }

        $qrCode = new QrCode(
            data: $menuUrl,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 10,
        );
        $svg = (new SvgWriter())->write($qrCode)->getString();

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
