@extends('layouts.main')

@section('title')
    {{ __('Inspection & Warranty Settings') }}
@endsection

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h4>@yield('title')</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('Global Settings') }}</h5>
            <p class="text-muted">{{ __('Configure inspection & warranty service settings. Changes reflect immediately in all apps.') }}</p>
        </div>
        <div class="card-body">
            <form id="inspection-settings-form" method="POST" action="{{ route('inspection-warranty.settings.update') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="is_active" class="form-label">
                                {{ __('Service Status') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    {{ $config->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('Enable Inspection & Warranty Service') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('When disabled, the service will not appear on product pages') }}
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="fee_percentage" class="form-label">
                                {{ __('Inspection & Warranty Fee Percentage') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="fee_percentage" name="fee_percentage" 
                                    value="{{ $config->fee_percentage }}" min="0" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('Default: 4%. This fee is calculated from device price and paid by buyer.') }}
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="warranty_duration" class="form-label">
                                {{ __('Warranty Duration (Days)') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="warranty_duration" name="warranty_duration" 
                                value="{{ $config->warranty_duration }}" min="1" max="365" required>
                            <small class="form-text text-muted">
                                {{ __('Default: 5 days. Warranty period starts from delivery date.') }}
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="service_description" class="form-label">
                                {{ __('Service Description') }}
                            </label>
                            <textarea class="form-control" id="service_description" name="service_description" rows="4"
                                placeholder="{{ __('Describe the inspection & warranty service...') }}">{{ $config->service_description }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('This description appears on the service overview page') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="workflow_steps" class="form-label">
                                {{ __('Inspection Workflow Steps') }}
                            </label>
                            <textarea class="form-control" id="workflow_steps" name="workflow_steps" rows="4"
                                placeholder="{{ __('Describe the inspection workflow steps...') }}">{{ $config->workflow_steps }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('Step-by-step explanation of the inspection process') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="terms_conditions" class="form-label">
                                {{ __('Warranty Terms & Conditions') }}
                            </label>
                            <textarea class="form-control rich-text-editor" id="terms_conditions" name="terms_conditions" rows="6">{{ $config->terms_conditions }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('Rich text warranty terms and conditions') }}
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="covered_items" class="form-label">
                                {{ __('Covered Issues') }}
                            </label>
                            <textarea class="form-control" id="covered_items" name="covered_items" rows="8"
                                placeholder="{{ __('Enter covered items, one per line...') }}">{{ is_array($config->covered_items) ? implode("\n", $config->covered_items) : $config->covered_items }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('List of issues covered under warranty (one per line)') }}
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="excluded_items" class="form-label">
                                {{ __('Excluded Issues') }}
                            </label>
                            <textarea class="form-control" id="excluded_items" name="excluded_items" rows="8"
                                placeholder="{{ __('Enter excluded items, one per line...') }}">{{ is_array($config->excluded_items) ? implode("\n", $config->excluded_items) : $config->excluded_items }}</textarea>
                            <small class="form-text text-muted">
                                {{ __('List of issues NOT covered under warranty (one per line)') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.getElementById('inspection-settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Convert textarea arrays to JSON arrays
    const coveredItems = formData.get('covered_items').split('\n').filter(item => item.trim());
    const excludedItems = formData.get('excluded_items').split('\n').filter(item => item.trim());
    
    formData.set('covered_items', JSON.stringify(coveredItems));
    formData.set('excluded_items', JSON.stringify(excludedItems));
    formData.set('is_active', document.getElementById('is_active').checked ? '1' : '0');

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error === false) {
            showToast('success', data.message || 'Settings updated successfully');
        } else {
            showToast('error', data.message || 'Failed to update settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred while updating settings');
    });
});
</script>
@endsection
