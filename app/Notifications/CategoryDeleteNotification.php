<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Category;

class CategoryDeleteNotification extends Notification
{
    use Queueable;

    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Category Deleted',
            'message' => "Category '{$this->category->name}' was deleted successfully.",
            'category_id' => $this->category->id,
        ];
    }
}
