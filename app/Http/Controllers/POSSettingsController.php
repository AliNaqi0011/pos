<?php

namespace App\Http\Controllers;

use App\Models\POSSetting;
use Illuminate\Http\Request;

class POSSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => POSSetting::get('store_name', 'My Store'),
            'store_address' => POSSetting::get('store_address', ''),
            'store_phone' => POSSetting::get('store_phone', ''),
            'store_email' => POSSetting::get('store_email', ''),
            'tax_rate' => POSSetting::get('tax_rate', '10'),
            'currency' => POSSetting::get('currency', '$'),
            'receipt_footer' => POSSetting::get('receipt_footer', 'Thank you for your business!'),
            'auto_print_receipt' => POSSetting::get('auto_print_receipt', '1'),
            'show_barcode_scanner' => POSSetting::get('show_barcode_scanner', '1'),
            'default_customer' => POSSetting::get('default_customer', 'Walk-in Customer'),
            'low_stock_alert' => POSSetting::get('low_stock_alert', '10'),
        ];
        
        return view('admin.pos-settings.index', compact('settings'));
    }
    
    public function update(Request $request)
    {
        $settings = [
            'store_name', 'store_address', 'store_phone', 'store_email',
            'tax_rate', 'currency', 'receipt_footer', 'auto_print_receipt',
            'show_barcode_scanner', 'default_customer', 'low_stock_alert'
        ];
        
        foreach ($settings as $key) {
            if ($request->has($key)) {
                POSSetting::set($key, $request->get($key));
            }
        }
        
        return redirect()->back()->with('success', 'POS settings updated successfully!');
    }
    
    public function getSettings()
    {
        return response()->json([
            'store_name' => POSSetting::get('store_name', 'My Store'),
            'store_address' => POSSetting::get('store_address', ''),
            'store_phone' => POSSetting::get('store_phone', ''),
            'store_email' => POSSetting::get('store_email', ''),
            'tax_rate' => floatval(POSSetting::get('tax_rate', '10')),
            'currency' => POSSetting::get('currency', '$'),
            'receipt_footer' => POSSetting::get('receipt_footer', 'Thank you for your business!'),
            'auto_print_receipt' => POSSetting::get('auto_print_receipt', '1') === '1',
            'show_barcode_scanner' => POSSetting::get('show_barcode_scanner', '1') === '1',
            'default_customer' => POSSetting::get('default_customer', 'Walk-in Customer'),
            'low_stock_alert' => intval(POSSetting::get('low_stock_alert', '10')),
        ]);
    }
}