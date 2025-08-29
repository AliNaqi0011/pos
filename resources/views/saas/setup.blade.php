@extends('layouts.main')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">SaaS Setup</h3>
                        <h6 class="font-weight-normal mb-0">Configure your SaaS tenant</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tenant Setup</h4>
                        <p class="card-description">Setup your SaaS tenant configuration</p>
                        
                        <form>
                            <div class="form-group">
                                <label>Tenant Name</label>
                                <input type="text" class="form-control" placeholder="Enter tenant name">
                            </div>
                            <div class="form-group">
                                <label>Domain</label>
                                <input type="text" class="form-control" placeholder="Enter domain">
                            </div>
                            <div class="form-group">
                                <label>Plan</label>
                                <select class="form-control">
                                    <option>Select Plan</option>
                                    <option>Monthly - PKR 1,200</option>
                                    <option>6 Months - PKR 5,599</option>
                                    <option>Yearly - PKR 10,000</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Setup Tenant</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection