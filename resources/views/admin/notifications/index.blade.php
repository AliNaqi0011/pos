@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All Notifications</h4>
                    <button class="btn btn-primary btn-sm" id="mark-all-read-page">Mark All as Read</button>
                </div>
                
                @if($notifications->count() > 0)
                    <div class="list-group">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            @php
                                                $type = class_basename($notification->type);
                                                $icon = 'typcn-info-large';
                                                $color = 'primary';
                                                
                                                if(str_contains($type, 'Create')) {
                                                    $icon = 'typcn-plus';
                                                    $color = 'success';
                                                } elseif(str_contains($type, 'Update')) {
                                                    $icon = 'typcn-edit';
                                                    $color = 'info';
                                                } elseif(str_contains($type, 'Delete')) {
                                                    $icon = 'typcn-trash';
                                                    $color = 'danger';
                                                } elseif(str_contains($type, 'User')) {
                                                    $icon = 'typcn-user-outline';
                                                } elseif(str_contains($type, 'Product')) {
                                                    $icon = 'typcn-shopping-cart';
                                                } elseif(str_contains($type, 'Sale')) {
                                                    $icon = 'typcn-chart-bar';
                                                } elseif(str_contains($type, 'Category')) {
                                                    $icon = 'typcn-th-menu';
                                                } elseif(str_contains($type, 'Brand')) {
                                                    $icon = 'typcn-briefcase';
                                                } elseif(str_contains($type, 'Customer')) {
                                                    $icon = 'typcn-group';
                                                } elseif(str_contains($type, 'Warehouse')) {
                                                    $icon = 'typcn-home';
                                                } elseif(str_contains($type, 'Blog')) {
                                                    $icon = 'typcn-document-add';
                                                } elseif(str_contains($type, 'Expense')) {
                                                    $icon = 'typcn-calculator';
                                                } elseif(str_contains($type, 'Quotation')) {
                                                    $icon = 'typcn-document';
                                                }
                                            @endphp
                                            <div class="bg-{{ $color }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="{{ $icon }}"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <div>
                                        @if(!$notification->read_at)
                                            <span class="badge badge-primary">New</span>
                                        @endif
                                        <button class="btn btn-sm btn-outline-secondary ml-2 mark-read-btn" data-id="{{ $notification->id }}">
                                            <i class="typcn typcn-tick"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="typcn typcn-bell" style="font-size: 64px; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">No notifications found</h5>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mark individual notification as read
    $('.mark-read-btn').click(function() {
        const notificationId = $(this).data('id');
        const button = $(this);
        
        $.post('{{ route("notifications.mark-read", "") }}/' + notificationId, {
            _token: '{{ csrf_token() }}'
        }, function() {
            button.closest('.list-group-item').removeClass('bg-light');
            button.siblings('.badge').remove();
            button.remove();
        });
    });
    
    // Mark all notifications as read
    $('#mark-all-read-page').click(function() {
        $.post('{{ route("notifications.mark-all-read") }}', {
            _token: '{{ csrf_token() }}'
        }, function() {
            location.reload();
        });
    });
});
</script>
@endsection