@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="mdi mdi-message"></i> Tenant Communication</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $communications['announcements_sent'] }}</h3><p>Announcements Sent</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $communications['newsletters'] }}</h3><p>Newsletters</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $communications['feature_updates'] }}</h3><p>Feature Updates</p></div></div></div>
                <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center"><h3>{{ $communications['maintenance_notices'] }}</h3><p>Maintenance Notices</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection