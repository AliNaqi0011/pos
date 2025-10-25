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
        try {
            $users = User::all();
            if ($users->isNotEmpty()) {
                Notification::send($users, new $notificationClass($data));
            }
        } catch (\Exception $e) {
            \Log::error('Notification sending failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to specific roles
     */
    protected function sendNotificationToRoles($notificationClass, $roles, $data = null)
    {
        try {
            $users = User::whereHas('roles', function($q) use ($roles) {
                $q->whereIn('name', (array) $roles);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new $notificationClass($data));
            }
        } catch (\Exception $e) {
            \Log::error('Role notification sending failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to admins only
     */
    protected function sendNotificationToAdmins($notificationClass, $data = null)
    {
        try {
            $users = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['super_admin', 'admin']);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new $notificationClass($data));
            }
        } catch (\Exception $e) {
            \Log::error('Admin notification sending failed: ' . $e->getMessage());
        }
    }
}