<?php

namespace App\Services;

use App\Models\UserSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FBRService
{
    protected $settings;

    public function __construct()
    {
        // Settings will be loaded when needed
        $this->settings = null;
    }

    public function isEnabled()
    {
        $this->loadSettings();
        return $this->settings && $this->settings->fbr_enabled;
    }

    public function submitInvoice($saleData)
    {
        $this->loadSettings();
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'FBR integration not enabled'];
        }

        try {
            $invoiceData = $this->formatInvoiceData($saleData);
            
            $response = Http::timeout(30)->post($this->settings->fbr_api_url . '/api/invoice', [
                'pos_id' => $this->settings->fbr_pos_id,
                'username' => $this->settings->fbr_username,
                'password' => $this->settings->fbr_password,
                'invoice_data' => $invoiceData
            ]);

            if ($response->successful()) {
                Log::info('FBR Invoice submitted successfully', ['sale_id' => $saleData['id']]);
                return ['success' => true, 'response' => $response->json()];
            } else {
                Log::error('FBR Invoice submission failed', ['response' => $response->body()]);
                return ['success' => false, 'message' => 'FBR submission failed'];
            }
        } catch (\Exception $e) {
            Log::error('FBR Service Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'FBR service error: ' . $e->getMessage()];
        }
    }

    private function formatInvoiceData($saleData)
    {
        return [
            'invoice_number' => $saleData['id'],
            'invoice_date' => $saleData['sale_date'],
            'total_amount' => $saleData['final_total'],
            'tax_amount' => $saleData['tax_amount'] ?? 0,
            'customer_name' => $saleData['customer']['name'] ?? 'Walk-in Customer',
            'items' => collect($saleData['items'])->map(function ($item) {
                return [
                    'item_name' => $item['product']['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product_price'],
                    'total_price' => $item['total']
                ];
            })->toArray()
        ];
    }

    public function testConnection()
    {
        $this->loadSettings();
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'FBR integration not enabled'];
        }

        try {
            $response = Http::timeout(10)->post($this->settings->fbr_api_url . '/api/test', [
                'pos_id' => $this->settings->fbr_pos_id,
                'username' => $this->settings->fbr_username,
                'password' => $this->settings->fbr_password
            ]);

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'FBR connection successful' : 'FBR connection failed'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }
    
    private function loadSettings()
    {
        if ($this->settings === null && auth()->check()) {
            $this->settings = UserSetting::where('user_id', auth()->id())->first();
        }
    }
}