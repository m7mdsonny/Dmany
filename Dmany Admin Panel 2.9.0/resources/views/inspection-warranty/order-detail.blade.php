@extends('layouts.main')

@section('title')
    {{ __('Inspection Order Details') }} - {{ $order->order_number }}
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
    <div class="row">
        <!-- Left Column: Order Info & Actions -->
        <div class="col-lg-4">
            <!-- A) Device & Deal Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Device & Deal Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ __('Order Number') }}:</strong>
                        <p>{{ $order->order_number }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Product') }}:</strong>
                        <p>{{ $order->item->name ?? 'N/A' }}</p>
                        @if($order->item && $order->item->image)
                            <img src="{{ $order->item->image }}" alt="Product" class="img-thumbnail mt-2" style="max-width: 200px;">
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Buyer') }}:</strong>
                        <p>{{ $order->buyer->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Seller') }}:</strong>
                        <p>{{ $order->seller->name ?? 'N/A' }}</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>{{ __('Device Price') }}:</strong>
                        <p class="text-primary">{{ number_format($order->device_price, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Inspection Fee') }}:</strong>
                        <p>{{ number_format($order->inspection_fee, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Total Amount') }}:</strong>
                        <p class="fw-bold text-success">{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Status & Actions Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Status & Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Status') }}</label>
                        <select class="form-select" id="order_status">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="device_received" {{ $order->status === 'device_received' ? 'selected' : '' }}>Device Received</option>
                            <option value="under_inspection" {{ $order->status === 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                            <option value="passed" {{ $order->status === 'passed' ? 'selected' : '' }}>Passed</option>
                            <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="warranty_active" {{ $order->status === 'warranty_active' ? 'selected' : '' }}>Warranty Active</option>
                            <option value="warranty_expired" {{ $order->status === 'warranty_expired' ? 'selected' : '' }}>Warranty Expired</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Assign Technician') }}</label>
                        <select class="form-select" id="technician_id">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $order->assigned_technician_id === $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }} ({{ $tech->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Admin Notes') }}</label>
                        <textarea class="form-control" id="admin_notes" rows="3" placeholder="{{ __('Internal notes (visible only to admins)...') }}">{{ $order->admin_notes }}</textarea>
                    </div>

                    <button type="button" class="btn btn-primary w-100" onclick="updateStatus()">
                        <i class="bi bi-save"></i> {{ __('Update Status') }}
                    </button>
                </div>
            </div>

            <!-- Dates Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Important Dates') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Order Created') }}:</small>
                        <p>{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    @if($order->device_received_at)
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Device Received') }}:</small>
                            <p>{{ $order->device_received_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    @endif
                    @if($order->inspection_date)
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Inspection Date') }}:</small>
                            <p>{{ $order->inspection_date->format('Y-m-d H:i:s') }}</p>
                        </div>
                    @endif
                    @if($order->delivery_date)
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Delivery Date') }}:</small>
                            <p>{{ $order->delivery_date->format('Y-m-d H:i:s') }}</p>
                        </div>
                    @endif
                    @if($order->warranty_start_date)
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Warranty Start') }}:</small>
                            <p>{{ $order->warranty_start_date->format('Y-m-d') }}</p>
                        </div>
                    @endif
                    @if($order->warranty_end_date)
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Warranty End') }}:</small>
                            <p>{{ $order->warranty_end_date->format('Y-m-d') }}</p>
                            @if($order->getRemainingWarrantyDays() !== null)
                                <p class="text-info">
                                    <strong>{{ __('Remaining') }}:</strong> {{ $order->getRemainingWarrantyDays() }} {{ __('days') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Inspection Details -->
        <div class="col-lg-8">
            <!-- B) Inspection Checklist (Editable) -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Inspection Checklist') }}</h5>
                    <p class="text-muted mb-0">{{ __('All fields are editable by admin/technician') }}</p>
                </div>
                <div class="card-body">
                    <form id="inspection-report-form" action="{{ route('inspection-warranty.report.submit', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Battery Health (%)') }}</label>
                                    <input type="number" class="form-control" name="battery_health" 
                                        value="{{ $order->inspectionReport->battery_health ?? '' }}" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Condition Score (1-10)') }}</label>
                                    <input type="number" class="form-control" name="condition_score" 
                                        value="{{ $order->inspectionReport->condition_score ?? '' }}" min="1" max="10">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Screen Condition') }}</label>
                                    <select class="form-select" name="screen_condition">
                                        <option value="">{{ __('Select...') }}</option>
                                        <option value="excellent" {{ ($order->inspectionReport->screen_condition ?? '') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="good" {{ ($order->inspectionReport->screen_condition ?? '') === 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ ($order->inspectionReport->screen_condition ?? '') === 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ ($order->inspectionReport->screen_condition ?? '') === 'poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Camera Condition') }}</label>
                                    <select class="form-select" name="camera_condition">
                                        <option value="">{{ __('Select...') }}</option>
                                        <option value="excellent" {{ ($order->inspectionReport->camera_condition ?? '') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="good" {{ ($order->inspectionReport->camera_condition ?? '') === 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ ($order->inspectionReport->camera_condition ?? '') === 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ ($order->inspectionReport->camera_condition ?? '') === 'poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Speaker/Mic Status') }}</label>
                                    <select class="form-select" name="speaker_status">
                                        <option value="">{{ __('Select...') }}</option>
                                        <option value="working" {{ ($order->inspectionReport->speaker_status ?? '') === 'working' ? 'selected' : '' }}>Working</option>
                                        <option value="partial" {{ ($order->inspectionReport->speaker_status ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="not_working" {{ ($order->inspectionReport->speaker_status ?? '') === 'not_working' ? 'selected' : '' }}>Not Working</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Network/WiFi Status') }}</label>
                                    <select class="form-select" name="network_status">
                                        <option value="">{{ __('Select...') }}</option>
                                        <option value="working" {{ ($order->inspectionReport->network_status ?? '') === 'working' ? 'selected' : '' }}>Working</option>
                                        <option value="partial" {{ ($order->inspectionReport->network_status ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="not_working" {{ ($order->inspectionReport->network_status ?? '') === 'not_working' ? 'selected' : '' }}>Not Working</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Grade') }}</label>
                            <select class="form-select" name="grade">
                                <option value="">{{ __('Select...') }}</option>
                                <option value="A" {{ ($order->inspectionReport->grade ?? '') === 'A' ? 'selected' : '' }}>A - Excellent</option>
                                <option value="B" {{ ($order->inspectionReport->grade ?? '') === 'B' ? 'selected' : '' }}>B - Good</option>
                                <option value="C" {{ ($order->inspectionReport->grade ?? '') === 'C' ? 'selected' : '' }}>C - Fair</option>
                                <option value="D" {{ ($order->inspectionReport->grade ?? '') === 'D' ? 'selected' : '' }}>D - Poor</option>
                                <option value="Fail" {{ ($order->inspectionReport->grade ?? '') === 'Fail' ? 'selected' : '' }}>Fail</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Technician Notes') }}</label>
                            <textarea class="form-control" name="technician_notes" rows="4" 
                                placeholder="{{ __('Detailed technician notes...') }}">{{ $order->inspectionReport->technician_notes ?? '' }}</textarea>
                        </div>

                        <hr>

                        <!-- D) Final Inspection Decision -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Final Decision') }} <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="final_decision" id="decision_pass" value="pass" 
                                    {{ ($order->inspectionReport->final_decision ?? '') === 'pass' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="decision_pass">
                                    <strong class="text-success">{{ __('Pass Inspection') }}</strong> - Deal continues, warranty starts
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="final_decision" id="decision_fail" value="fail" 
                                    {{ ($order->inspectionReport->final_decision ?? '') === 'fail' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="decision_fail">
                                    <strong class="text-danger">{{ __('Fail Inspection') }}</strong> - Deal cancelled, payment refunded
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Decision Notes') }}</label>
                            <textarea class="form-control" name="decision_notes" rows="3" 
                                placeholder="{{ __('Notes for the decision...') }}">{{ $order->inspectionReport->decision_notes ?? '' }}</textarea>
                        </div>

                        <!-- C) Attachments -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Inspection Images/Logs') }}</label>
                            <input type="file" class="form-control" name="images[]" multiple accept="image/*,.pdf,.txt">
                            <small class="form-text text-muted">{{ __('Upload inspection photos, diagnostic logs, or reports') }}</small>
                        </div>

                        @if($order->inspectionReport && $order->inspectionReport->images->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">{{ __('Current Images') }}</label>
                                <div class="row">
                                    @foreach($order->inspectionReport->images as $image)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ $image->image_url }}" alt="Inspection Image" class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger mt-1 w-100" onclick="deleteImage({{ $order->inspectionReport->id }}, {{ $image->id }})">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> {{ __('Submit Inspection Report') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Audit Trail -->
            @if($order->auditLogs->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Audit Trail') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Admin') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->auditLogs->sortByDesc('created_at') as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ $log->action_type }}</span></td>
                                            <td>{{ $log->action_description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
function updateStatus() {
    const status = document.getElementById('order_status').value;
    const technicianId = document.getElementById('technician_id').value;
    const adminNotes = document.getElementById('admin_notes').value;

    const formData = new FormData();
    formData.append('status', status);
    formData.append('technician_id', technicianId);
    formData.append('notes', adminNotes);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("inspection-warranty.orders.update-status", $order->id) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error === false) {
            showToast('success', data.message || 'Status updated successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Failed to update status');
        }
    });
}

document.getElementById('inspection-report-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error === false) {
            showToast('success', data.message || 'Inspection report submitted successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Failed to submit report');
        }
    });
});

function deleteImage(reportId, imageId) {
    if (!confirm('Are you sure you want to delete this image?')) return;
    
    fetch(`{{ route('inspection-warranty.report.delete-image', ['reportId' => ':reportId', 'imageId' => ':imageId']) }}`
        .replace(':reportId', reportId).replace(':imageId', imageId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error === false) {
            showToast('success', 'Image deleted successfully');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast('error', data.message || 'Failed to delete image');
        }
    });
}
</script>
@endsection
