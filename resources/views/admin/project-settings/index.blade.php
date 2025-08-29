@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project Settings</li>
        </ol>
    </nav>
    
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Project Settings</h4>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <form class="forms-sample" action="{{ route('admin.project.settings.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_name">Application Name</label>
                                    <input type="text" name="app_name" class="form-control" id="app_name" 
                                           value="{{ $settings['app_name'] }}" placeholder="Enter App Name">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" id="company_name" 
                                           value="{{ $settings['company_name'] }}" placeholder="Enter Company Name">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_logo">Application Logo</label>
                                    <input type="file" name="app_logo" class="form-control" id="app_logo" accept="image/*">
                                    @if($settings['app_logo'])
                                        <small class="text-muted">Current: <img src="{{ asset($settings['app_logo']) }}" height="30" alt="Logo"></small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_favicon">Favicon</label>
                                    <input type="file" name="app_favicon" class="form-control" id="app_favicon" accept="image/*">
                                    @if($settings['app_favicon'])
                                        <small class="text-muted">Current: <img src="{{ asset($settings['app_favicon']) }}" height="16" alt="Favicon"></small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="company_address">Company Address</label>
                            <textarea name="company_address" class="form-control" id="company_address" rows="3" 
                                      placeholder="Enter Company Address">{{ $settings['company_address'] }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">Company Phone</label>
                                    <input type="text" name="company_phone" class="form-control" id="company_phone" 
                                           value="{{ $settings['company_phone'] }}" placeholder="Enter Company Phone">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">Company Email</label>
                                    <input type="email" name="company_email" class="form-control" id="company_email" 
                                           value="{{ $settings['company_email'] }}" placeholder="Enter Company Email">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_website">Company Website</label>
                                    <input type="url" name="company_website" class="form-control" id="company_website" 
                                           value="{{ $settings['company_website'] }}" placeholder="https://example.com">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_number">Tax Number</label>
                                    <input type="text" name="tax_number" class="form-control" id="tax_number" 
                                           value="{{ $settings['tax_number'] }}" placeholder="Enter Tax Number">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Currency</label>
                                    <select name="currency" class="form-control" id="currency">
                                        <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        <option value="PKR" {{ $settings['currency'] == 'PKR' ? 'selected' : '' }}>PKR - Pakistani Rupee</option>
                                        <option value="INR" {{ $settings['currency'] == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timezone">Timezone</label>
                                    <select name="timezone" class="form-control" id="timezone">
                                        <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Asia/Karachi" {{ $settings['timezone'] == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi</option>
                                        <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                        <option value="Europe/London" {{ $settings['timezone'] == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_format">Date Format</label>
                                    <select name="date_format" class="form-control" id="date_format">
                                        <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="d-m-Y" {{ $settings['date_format'] == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="time_format">Time Format</label>
                                    <select name="time_format" class="form-control" id="time_format">
                                        <option value="H:i:s" {{ $settings['time_format'] == 'H:i:s' ? 'selected' : '' }}>24 Hour (HH:MM:SS)</option>
                                        <option value="h:i:s A" {{ $settings['time_format'] == 'h:i:s A' ? 'selected' : '' }}>12 Hour (hh:mm:ss AM/PM)</option>
                                        <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24 Hour (HH:MM)</option>
                                        <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Hour (hh:mm AM/PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">FBR Integration Settings</h5>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="fbr_enabled" class="form-check-input" id="fbr_enabled" 
                                   {{ $settings['fbr_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="fbr_enabled">
                                Enable FBR Integration
                            </label>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fbr_pos_id">FBR POS ID</label>
                                    <input type="text" name="fbr_pos_id" class="form-control" id="fbr_pos_id" 
                                           value="{{ $settings['fbr_pos_id'] }}" placeholder="Enter FBR POS ID">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fbr_username">FBR Username</label>
                                    <input type="text" name="fbr_username" class="form-control" id="fbr_username" 
                                           value="{{ $settings['fbr_username'] }}" placeholder="Enter FBR Username">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fbr_password">FBR Password</label>
                                    <input type="password" name="fbr_password" class="form-control" id="fbr_password" 
                                           value="{{ $settings['fbr_password'] }}" placeholder="Enter FBR Password">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fbr_api_url">FBR API URL</label>
                                    <input type="url" name="fbr_api_url" class="form-control" id="fbr_api_url" 
                                           value="{{ $settings['fbr_api_url'] }}" placeholder="https://esp.fbr.gov.pk">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mr-2">Update Settings</button>
                        <button type="button" class="btn btn-info mr-2" id="test-fbr-btn">Test FBR Connection</button>
                        <button type="reset" class="btn btn-light">Reset</button>
                        
                        <script>
                        document.getElementById('test-fbr-btn').addEventListener('click', function() {
                            fetch('{{ route('admin.project.settings.test-fbr') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                alert(data.message);
                            })
                            .catch(error => {
                                alert('Error testing FBR connection');
                            });
                        });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection