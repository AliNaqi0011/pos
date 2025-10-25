<?php

namespace App\Services;

use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerCommunication;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send($user, string $message, string $type = 'info', array $data = []): bool
    {
        try {
            \DB::table('notifications')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\GeneralNotification',
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id,
                'data' => json_encode(['message' => $message, 'type' => $type]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }
    public function sendSaleReceipt(int $saleId, string $method = 'email'): bool
    {
        $sale = \App\Models\Sale::with(['customer', 'saleItems.product'])->findOrFail($saleId);
        
        if (!$sale->customer) {
            return false;
        }
        
        return match($method) {
            'email' => $this->sendEmailReceipt($sale),
            'sms' => $this->sendSMSReceipt($sale),
            'whatsapp' => $this->sendWhatsAppReceipt($sale),
            default => false,
        };
    }

    public function sendLowStockAlert(array $products): void
    {
        $admins = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();
        
        foreach ($admins as $admin) {
            $this->sendEmail(
                $admin->email,
                'Low Stock Alert',
                'emails.low-stock-alert',
                ['products' => $products, 'user' => $admin]
            );
        }
    }

    public function sendCustomerBirthdayGreeting(Customer $customer): bool
    {
        $message = "Happy Birthday {$customer->name}! 🎉 Enjoy a special 10% discount on your next purchase. Use code: BIRTHDAY10";
        
        $sent = false;
        
        // Send via multiple channels
        if ($customer->email) {
            $sent = $this->sendEmail(
                $customer->email,
                'Happy Birthday!',
                'emails.birthday-greeting',
                ['customer' => $customer]
            ) || $sent;
        }
        
        if ($customer->phone) {
            $sent = $this->sendSMS($customer->phone, $message) || $sent;
        }
        
        // Record communication
        if ($sent) {
            $this->recordCommunication($customer, 'birthday_greeting', $message);
        }
        
        return $sent;
    }

    public function sendPromotionalCampaign(array $customerIds, array $campaignData): array
    {
        $results = [];
        $customers = Customer::whereIn('id', $customerIds)->get();
        
        foreach ($customers as $customer) {
            $sent = false;
            
            try {
                if ($campaignData['channels']['email'] && $customer->email) {
                    $sent = $this->sendEmail(
                        $customer->email,
                        $campaignData['subject'],
                        'emails.promotional-campaign',
                        ['customer' => $customer, 'campaign' => $campaignData]
                    ) || $sent;
                }
                
                if ($campaignData['channels']['sms'] && $customer->phone) {
                    $sent = $this->sendSMS($customer->phone, $campaignData['sms_message']) || $sent;
                }
                
                if ($sent) {
                    $this->recordCommunication($customer, 'promotional_campaign', $campaignData['message']);
                }
                
                $results[] = [
                    'customer_id' => $customer->id,
                    'status' => $sent ? 'sent' : 'failed',
                ];
                
            } catch (\Exception $e) {
                Log::error("Campaign send failed for customer {$customer->id}: " . $e->getMessage());
                $results[] = [
                    'customer_id' => $customer->id,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }

    public function sendOrderStatusUpdate(int $orderId, string $status): bool
    {
        // This would be for purchase orders or special orders
        $order = \App\Models\Purchase::with('supplier')->findOrFail($orderId);
        
        $message = "Order #{$order->id} status updated to: " . ucfirst($status);
        
        // Notify relevant users
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();
        
        foreach ($users as $user) {
            $this->sendEmail(
                $user->email,
                'Order Status Update',
                'emails.order-status-update',
                ['order' => $order, 'status' => $status, 'user' => $user]
            );
        }
        
        return true;
    }

    public function sendPaymentReminder(Customer $customer, float $amount): bool
    {
        $message = "Dear {$customer->name}, you have an outstanding balance of " . 
                  number_format($amount, 2) . ". Please settle your account at your earliest convenience.";
        
        $sent = false;
        
        if ($customer->email) {
            $sent = $this->sendEmail(
                $customer->email,
                'Payment Reminder',
                'emails.payment-reminder',
                ['customer' => $customer, 'amount' => $amount]
            ) || $sent;
        }
        
        if ($customer->phone) {
            $sent = $this->sendSMS($customer->phone, $message) || $sent;
        }
        
        if ($sent) {
            $this->recordCommunication($customer, 'payment_reminder', $message);
        }
        
        return $sent;
    }

    private function sendEmailReceipt(\App\Models\Sale $sale): bool
    {
        try {
            Mail::send('emails.sale-receipt', ['sale' => $sale], function($message) use ($sale) {
                $message->to($sale->customer->email, $sale->customer->name)
                       ->subject("Receipt for Sale #{$sale->id}");
            });
            
            $this->recordCommunication(
                $sale->customer, 
                'email', 
                "Sale receipt sent for order #{$sale->id}"
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error("Email receipt failed: " . $e->getMessage());
            return false;
        }
    }

    private function sendSMSReceipt(\App\Models\Sale $sale): bool
    {
        $message = "Thank you for your purchase! Receipt for Sale #{$sale->id}. " .
                  "Total: " . number_format($sale->final_total, 2) . ". " .
                  "Visit us again soon!";
        
        return $this->sendSMS($sale->customer->phone, $message);
    }

    private function sendWhatsAppReceipt(\App\Models\Sale $sale): bool
    {
        // WhatsApp Business API integration
        $message = "🧾 *Receipt - Sale #{$sale->id}*\\n\\n" .
                  "Thank you {$sale->customer->name}!\\n" .
                  "Total: " . number_format($sale->final_total, 2) . "\\n\\n" .
                  "Items:\\n";
        
        foreach ($sale->saleItems as $item) {
            $message .= "• {$item->product->name} x{$item->quantity}\\n";
        }
        
        return $this->sendWhatsApp($sale->customer->phone, $message);
    }

    private function sendEmail(string $to, string $subject, string $template, array $data): bool
    {
        try {
            Mail::send($template, $data, function($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    private function sendSMS(string $phone, string $message): bool
    {
        try {
            // Twilio integration
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            
            $twilio->messages->create($phone, [
                'from' => config('services.twilio.from'),
                'body' => $message
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("SMS send failed: " . $e->getMessage());
            return false;
        }
    }

    private function sendWhatsApp(string $phone, string $message): bool
    {
        try {
            // WhatsApp Business API integration
            $response = Http::post('https://api.whatsapp.com/send', [
                'phone' => $phone,
                'text' => $message,
                'apikey' => config('services.whatsapp.api_key'),
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: " . $e->getMessage());
            return false;
        }
    }

    private function recordCommunication(Customer $customer, string $type, string $message): void
    {
        CustomerCommunication::create([
            'customer_id' => $customer->id,
            'type' => $type,
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
            'created_by' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);
    }
}