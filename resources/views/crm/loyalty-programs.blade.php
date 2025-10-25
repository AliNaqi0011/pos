@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>🎯 Loyalty Programs</h4>
                <button class="btn btn-primary btn-sm float-right">Create Program</button>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body">
                                <h5>Active Programs</h5>
                                <h3>3</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body">
                                <h5>Total Members</h5>
                                <h3>1,245</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-gradient-info text-white">
                            <div class="card-body">
                                <h5>Points Redeemed</h5>
                                <h3>25,680</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Type</th>
                                <th>Members</th>
                                <th>Points Rate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gold Membership</td>
                                <td>Tier-based</td>
                                <td>450</td>
                                <td>1 point per $1</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">Edit</button>
                                    <button class="btn btn-sm btn-warning">Pause</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Silver Membership</td>
                                <td>Tier-based</td>
                                <td>650</td>
                                <td>1 point per $2</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">Edit</button>
                                    <button class="btn btn-sm btn-warning">Pause</button>
                                </td>
                            </tr>
                            <tr>
                                <td>VIP Program</td>
                                <td>Exclusive</td>
                                <td>145</td>
                                <td>2 points per $1</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">Edit</button>
                                    <button class="btn btn-sm btn-warning">Pause</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection