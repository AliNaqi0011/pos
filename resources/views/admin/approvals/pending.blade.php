@extends('layouts.main')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Pending Approvals</h3>
                        <h6 class="font-weight-normal mb-0">
                            @if(auth()->user()->hasRole('super_admin'))
                                Admin accounts waiting for approval
                            @else
                                Seller accounts waiting for approval
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Registration Date</th>
                                        @if(auth()->user()->hasRole('super_admin'))
                                        <th>Payment Status</th>
                                        @endif
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $user->roles->first()->name ?? 'No Role' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        @if(auth()->user()->hasRole('super_admin'))
                                        <td>
                                            @if($user->payment_verified)
                                                <span class="badge badge-success">Verified</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                                <button class="btn btn-sm btn-outline-success ml-2" 
                                                        onclick="verifyPayment({{ $user->id }})">
                                                    Verify Payment
                                                </button>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if(auth()->user()->hasRole('super_admin') && !$user->payment_verified)
                                                <span class="text-muted">Payment verification required</span>
                                            @else
                                                <button class="btn btn-success btn-sm" onclick="approveUser({{ $user->id }})">
                                                    Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm ml-2" onclick="rejectUser({{ $user->id }})">
                                                    Reject
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No pending approvals</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rejection Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveUser(userId) {
    if (confirm('Are you sure you want to approve this user?')) {
        fetch(`/admin/approvals/${userId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function rejectUser(userId) {
    document.getElementById('rejectForm').action = `/admin/approvals/${userId}/reject`;
    $('#rejectModal').modal('show');
}

function verifyPayment(userId) {
    if (confirm('Mark payment as verified?')) {
        fetch(`/admin/approvals/${userId}/verify-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection