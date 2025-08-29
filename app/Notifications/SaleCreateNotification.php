<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SaleCreateNotification extends Notification
{
    use Queueable;

    protected $sale;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Sale  $sale
     * @return void
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Send via mail and store in database
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  object  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('A new sale has been made in the system.')
                    ->line('Sale ID: ' . $this->sale->id)
                    ->line('Customer: ' . $this->sale->customer->name)
                    ->line('Total Amount: ' . $this->sale->final_total)
                    ->line('Payment Status: ' . $this->sale->payment_status)
                    ->line('Sale Date: ' . $this->sale->sale_date->format('Y-m-d H:i:s'))
                    ->action('View Sale', url('/sales/' . $this->sale->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  object  $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sale_id' => $this->sale->id,
            'customer_name' => $this->sale->customer->name,
            'total_amount' => $this->sale->final_total,
            'payment_status' => $this->sale->payment_status,
            'sale_date' => $this->sale->sale_date->format('Y-m-d H:i:s'),
            'sale_url' => url('/sales/' . $this->sale->id),
        ];
    }
}
