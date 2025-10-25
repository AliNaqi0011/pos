@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.settings') }}">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Listing</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">My {{ auth()->user()->role === 'super_admin' ? 'Admins' : 'Sellers' }}</h4>
                        <div class="mb-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="typcn typcn-plus"></i> Create New {{ auth()->user()->role === 'super_admin' ? 'Admin' : 'Seller' }}
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User Image</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="py-1">
                                                @if ($user->profile_image)
                                                    <img src="{{ asset('template/images/profile-images/' . $user->profile_image) }}"
                                                        class="rounded-circle" alt="User Profile Image" width="40" height="40">
                                                @else
                                                    <img src="{{ asset('template/images/profile-images/avatar7.png') }}"
                                                        alt="User" class="rounded-circle" width="40" height="40">
                                                @endif
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone_number }}</td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{route('users.edit',$user->id)}}" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="typcn typcn-edit"></i>
                                                </a>
                                                <a href="{{route('users.delete',$user->id)}}" class="btn btn-sm btn-danger" title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="typcn typcn-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="py-4">
                                                    <i class="typcn typcn-user-outline" style="font-size: 48px; color: #ccc;"></i>
                                                    <p class="mt-2 text-muted">No {{ auth()->user()->role === 'super_admin' ? 'admins' : 'sellers' }} created yet.</p>
                                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                        Create First {{ auth()->user()->role === 'super_admin' ? 'Admin' : 'Seller' }}
                                                    </a>
                                                </div>
                                            </td>
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
@endsection
