@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notifications</h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="d-flex align-items-start mb-3 p-3 border rounded {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ class_basename($notification->type) }}</h6>
                                    <p class="mb-1 text-muted">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if(!$notification->read_at)
                                    <button class="btn btn-sm btn-outline-primary mark-read" data-id="{{ $notification->id }}">
                                        Mark as Read
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        
                        {{ $notifications->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications yet</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mark-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            
            fetch(`/notifications/mark-read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });
});
</script>
@endsection