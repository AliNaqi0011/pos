<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseCategoryDeleteNotification extends Notification
{
    use Queueable;

    protected $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Expense category "' . $this->category->name . '" was deleted',
            'category_id' => $this->category->id,
            'action' => 'deleted'
        ];
    }
}