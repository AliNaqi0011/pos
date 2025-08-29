<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

trait SendsNotifications
{
    /**
     * Send notification to all users
     */
    protected function sendNotificationToAll($notificationClass, $data = null)
    {
        $users = User::all();
        Notification::send($users, new $notificationClass($data));
    }

    /**
     * Send notification to specific roles
     */
    protected function sendNotificationToRoles($notificationClass, $roles, $data = null)
    {
        $users = User::role($roles)->get();
        Notification::send($users, new $notificationClass($data));
    }

    /**
     * Send notification to admins only
     */
    protected function sendNotificationToAdmins($notificationClass, $data = null)
    {
        $users = User::role(['super_admin', 'admin'])->get();
        Notification::send($users, new $notificationClass($data));
    }
}