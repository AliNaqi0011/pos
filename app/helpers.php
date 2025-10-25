<?php

if (!function_exists('currency')) {
    function currency($amount) {
        return 'PKR ' . number_format($amount, 2);
    }
}

if (!function_exists('calculatePercentage')) {
    function calculatePercentage($value, $total) {
        return $total > 0 ? ($value / $total) * 100 : 0;
    }
}

if (!function_exists('calculateTax')) {
    function calculateTax($amount, $taxRate) {
        return ($amount * $taxRate) / 100;
    }
}

if (!function_exists('calculateDiscount')) {
    function calculateDiscount($amount, $discountRate) {
        return ($amount * $discountRate) / 100;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        return \Carbon\Carbon::parse($date)->format('d M Y');
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 2);
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($text) {
        return \Illuminate\Support\Str::slug($text);
    }
}

if (!function_exists('isValidBarcode')) {
    function isValidBarcode($barcode) {
        return preg_match('/^\d{13}$/', $barcode) === 1;
    }
}