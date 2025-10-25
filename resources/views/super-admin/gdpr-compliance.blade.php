@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="mdi mdi-shield-check"></i> GDPR Compliance</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $compliance['data_requests'] }}</h3><p>Data Requests</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $compliance['deletions_processed'] }}</h3><p>Deletions Processed</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ $compliance['consent_rate'] }}%</h3><p>Consent Rate</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $compliance['last_audit']->diffForHumans() }}</h3><p>Last Audit</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection