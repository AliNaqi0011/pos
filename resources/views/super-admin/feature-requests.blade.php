@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-purple text-white">
            <h4 class="mb-0"><i class="mdi mdi-lightbulb"></i> Feature Requests</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Feature</th><th>Votes</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr><td>{{ $request['feature'] }}</td><td><span class="badge badge-primary">{{ $request['votes'] }}</span></td><td><span class="badge badge-info">{{ $request['status'] }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection