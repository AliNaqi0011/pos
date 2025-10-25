@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.settings') }}">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit {{ ucfirst($user->role) }} - {{ $user->name }}</h4>
                        <form class="forms-sample" action="{{route('users.update')}}" method="post">
                            @csrf
                            <input type="hidden" value="{{$user->id}}" name="id">
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" id="name"
                                       value="{{$user->name}}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                      value="{{$user->email}}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" id="phone_number"
                                       value="{{$user->phone_number}}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">New Password (leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control" id="password"
                                       placeholder="Enter new password">
                            </div>
                            
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                                <small class="text-muted">Role cannot be changed after creation.</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Created Date</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y H:i A') }}" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update User</button>
                            <a href="{{ route('users') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
