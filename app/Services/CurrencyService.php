<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function convertAmount(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $fromRate = $this->getExchangeRate($fromCurrency);
        $toRate = $this->getExchangeRate($toCurrency);
        
        // Convert to base currency first, then to target currency
        $baseAmount = $amount / $fromRate;
        return $baseAmount * $toRate;
    }

    public function getExchangeRate(string $currencyCode): float
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            throw new \Exception("Currency {$currencyCode} not found");
        }
        
        return $currency->exchange_rate;
    }

    public function updateExchangeRates(): array
    {
        $baseCurrency = Currency::where('is_base', true)->first();
        
        if (!$baseCurrency) {
            throw new \Exception('No base currency configured');
        }
        
        $currencies = Currency::where('is_active', true)
            ->where('code', '!=', $baseCurrency->code)
            ->get();
            
        $updated = [];
        
        foreach ($currencies as $currency) {
            try {
                $rate = $this->fetchExchangeRate($baseCurrency->code, $currency->code);
                
                $currency->update([
                    'exchange_rate' => $rate,
                    'updated_at' => now(),
                ]);
                
                $updated[] = [
                    'currency' => $currency->code,
                    'old_rate' => $currency->getOriginal('exchange_rate'),
                    'new_rate' => $rate,
                ];
                
            } catch (\Exception $e) {
                \Log::error("Failed to update exchange rate for {$currency->code}: " . $e->getMessage());
            }
        }
        
        // Clear cache
        Cache::forget('exchange_rates');
        
        return $updated;
    }

    public function format(float $amount, string $currencyCode = 'PKR'): string
    {
        return 'PKR ' . number_format($amount, 2);
    }
    
    public function formatCurrency(float $amount, string $currencyCode): string
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            return number_format($amount, 2);
        }
        
        return $currency->symbol . ' ' . number_format($amount, 2);
    }

    public function getActiveCurrencies(): array
    {
        return Cache::remember('active_currencies', 3600, function () {
            return Currency::where('is_active', true)
                ->orderBy('code')
                ->get(['code', 'name', 'symbol', 'exchange_rate'])
                ->toArray();
        });
    }

    public function addCurrency(array $data): Currency
    {
        // Ensure only one base currency
        if ($data['is_base'] ?? false) {
            Currency::where('is_base', true)->update(['is_base' => false]);
        }
        
        return Currency::create($data);
    }

    private function fetchExchangeRate(string $from, string $to): float
    {
        // Using a free exchange rate API (you can replace with your preferred provider)
        $cacheKey = "exchange_rate_{$from}_{$to}";
        
        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            try {
                $response = Http::timeout(10)->get("https://api.exchangerate-api.com/v4/latest/{$from}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'][$to] ?? 1.0;
                }
                
                // Fallback to stored rate if API fails
                return Currency::where('code', $to)->value('exchange_rate') ?? 1.0;
                
            } catch (\Exception $e) {
                \Log::error("Exchange rate API error: " . $e->getMessage());
                return Currency::where('code', $to)->value('exchange_rate') ?? 1.0;
            }
        });
    }
}