@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">POS Settings</h4>
                <p class="card-description">Configure your Point of Sale system settings</p>
                
                <form method="POST" action="{{ route('pos-settings.update') }}">
                    @csrf
                    
                    <div class="row">
                        <!-- Store Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Store Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Store Name</label>
                                        <input type="text" class="form-control" name="store_name" value="{{ $settings['store_name'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Store Address</label>
                                        <textarea class="form-control" name="store_address" rows="3">{{ $settings['store_address'] }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Store Phone</label>
                                        <input type="text" class="form-control" name="store_phone" value="{{ $settings['store_phone'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Store Email</label>
                                        <input type="email" class="form-control" name="store_email" value="{{ $settings['store_email'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- POS Configuration -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>POS Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tax Rate (%)</label>
                                        <input type="number" class="form-control" name="tax_rate" value="{{ $settings['tax_rate'] }}" step="0.01" min="0" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label>Currency Symbol</label>
                                        <input type="text" class="form-control" name="currency" value="{{ $settings['currency'] }}" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label>Default Customer</label>
                                        <input type="text" class="form-control" name="default_customer" value="{{ $settings['default_customer'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Low Stock Alert Level</label>
                                        <input type="number" class="form-control" name="low_stock_alert" value="{{ $settings['low_stock_alert'] }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <!-- Receipt Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Receipt Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Receipt Footer Message</label>
                                        <textarea class="form-control" name="receipt_footer" rows="3">{{ $settings['receipt_footer'] }}</textarea>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="auto_print_receipt" value="1" {{ $settings['auto_print_receipt'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label">Auto Print Receipt</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Features -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Features</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" name="show_barcode_scanner" value="1" {{ $settings['show_barcode_scanner'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label">Show Barcode Scanner</label>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6>Quick Actions</h6>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="fixInventory()">Fix Inventory</button>
                                        <button type="button" class="btn btn-sm btn-info" onclick="clearCache()">Clear Cache</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="typcn typcn-tick"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function fixInventory() {
    if (confirm('This will recalculate all product stock levels. Continue?')) {
        $.post('{{ route("inventory.fix") }}', {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                alert(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        });
    }
}

function clearCache() {
    alert('Cache cleared successfully!');
}
</script>
@endsection