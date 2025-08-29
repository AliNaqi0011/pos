<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }

    public function getLatest()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => class_basename($notification->type),
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type)
                ];
            });

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    private function getNotificationIcon($type)
    {
        $icons = [
            'UserCreateNotification' => 'typcn-user-add',
            'UserUpdateNotification' => 'typcn-user-outline',
            'UserDeleteNotification' => 'typcn-user-delete',
            'ProductCreateNotification' => 'typcn-shopping-cart',
            'ProductUpdateNotification' => 'typcn-edit',
            'ProductDeleteNotification' => 'typcn-trash',
            'SaleCreateNotification' => 'typcn-chart-bar',
            'CategoryCreateNotification' => 'typcn-th-menu',
            'CategoryUpdateNotification' => 'typcn-edit',
            'CategoryDeleteNotification' => 'typcn-trash',
            'BrandCreateNotification' => 'typcn-briefcase',
            'BrandUpdateNotification' => 'typcn-edit',
            'BrandDeleteNotification' => 'typcn-trash',
            'CustomerCreateNotification' => 'typcn-group',
            'CustomerUpdateNotification' => 'typcn-edit',
            'CustomerDeleteNotification' => 'typcn-trash',
            'WarehouseCreateNotification' => 'typcn-home',
            'WarehouseUpdateNotification' => 'typcn-edit',
            'WarehouseDeleteNotification' => 'typcn-trash',
            'BlogCreateNotification' => 'typcn-document-add',
            'BlogUpdateNotification' => 'typcn-edit',
            'BlogDeleteNotification' => 'typcn-trash',
            'ExpenseCreateNotification' => 'typcn-calculator',
            'ExpenseUpdateNotification' => 'typcn-edit',
            'ExpenseDeleteNotification' => 'typcn-trash',
            'QuotationCreated' => 'typcn-document',
            'QuotationApproved' => 'typcn-tick',
            'QuotationRejected' => 'typcn-times'
        ];

        return $icons[class_basename($type)] ?? 'typcn-info-large';
    }

    private function getNotificationColor($type)
    {
        $colors = [
            'UserCreateNotification' => 'success',
            'UserUpdateNotification' => 'info',
            'UserDeleteNotification' => 'danger',
            'ProductCreateNotification' => 'success',
            'ProductUpdateNotification' => 'info',
            'ProductDeleteNotification' => 'danger',
            'SaleCreateNotification' => 'success',
            'CategoryCreateNotification' => 'success',
            'CategoryUpdateNotification' => 'info',
            'CategoryDeleteNotification' => 'danger',
            'BrandCreateNotification' => 'success',
            'BrandUpdateNotification' => 'info',
            'BrandDeleteNotification' => 'danger',
            'CustomerCreateNotification' => 'success',
            'CustomerUpdateNotification' => 'info',
            'CustomerDeleteNotification' => 'danger',
            'WarehouseCreateNotification' => 'success',
            'WarehouseUpdateNotification' => 'info',
            'WarehouseDeleteNotification' => 'danger',
            'BlogCreateNotification' => 'success',
            'BlogUpdateNotification' => 'info',
            'BlogDeleteNotification' => 'danger',
            'ExpenseCreateNotification' => 'warning',
            'ExpenseUpdateNotification' => 'info',
            'ExpenseDeleteNotification' => 'danger',
            'QuotationCreated' => 'primary',
            'QuotationApproved' => 'success',
            'QuotationRejected' => 'danger'
        ];

        return $colors[class_basename($type)] ?? 'info';
    }
}