@extends('layouts.main')

@section('title')
    {{ __('Warranty Claim Details') }} - {{ $claim->claim_number }}
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
        <!-- Left Column: Claim Info -->
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Claim Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ __('Claim Number') }}:</strong>
                        <p>{{ $claim->claim_number }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Order Number') }}:</strong>
                        <p>
                            <a href="{{ route('inspection-warranty.orders.detail', $claim->inspection_order_id) }}">
                                {{ $claim->inspectionOrder->order_number ?? 'N/A' }}
                            </a>
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Product') }}:</strong>
                        <p>{{ $claim->inspectionOrder->item->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Buyer') }}:</strong>
                        <p>{{ $claim->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Status') }}:</strong>
                        <p>
                            @if($claim->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($claim->status === 'under_review')
                                <span class="badge bg-info">Under Review</span>
                            @elseif($claim->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($claim->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($claim->status === 'resolved')
                                <span class="badge bg-success">Resolved</span>
                            @else
                                <span class="badge bg-dark">Cancelled</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('Created At') }}:</strong>
                        <p>{{ $claim->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    @if($claim->resolved_at)
                        <div class="mb-3">
                            <strong>{{ __('Resolved At') }}:</strong>
                            <p>{{ $claim->resolved_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>{{ __('Resolved By') }}:</strong>
                            <p>{{ $claim->resolvedBy->name ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Inspection Report Comparison -->
            @if($claim->inspectionOrder->inspectionReport)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Inspection Report Baseline') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Condition Score') }}:</small>
                            <p><strong>{{ $claim->inspectionOrder->inspectionReport->condition_score ?? 'N/A' }}/10</strong></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Grade') }}:</small>
                            <p><strong>{{ $claim->inspectionOrder->inspectionReport->grade ?? 'N/A' }}</strong></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Battery Health') }}:</small>
                            <p><strong>{{ $claim->inspectionOrder->inspectionReport->battery_health ?? 'N/A' }}%</strong></p>
                        </div>
                        @if($claim->inspectionOrder->inspectionReport->technician_notes)
                            <div class="mb-2">
                                <small class="text-muted">{{ __('Technician Notes') }}:</small>
                                <p class="small">{{ Str::limit($claim->inspectionOrder->inspectionReport->technician_notes, 100) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Claim Details & Resolution -->
        <div class="col-lg-7">
            <!-- Claim Description -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Claim Description') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $claim->description }}</p>
                </div>
            </div>

            <!-- Claim Images -->
            @if($claim->images->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Claim Images') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($claim->images as $image)
                                <div class="col-md-4 mb-2">
                                    <img src="{{ $image->image_url }}" alt="Claim Image" 
                                        class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Resolution -->
            @if(in_array($claim->status, ['pending', 'under_review']))
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Resolve Claim') }}</h5>
                    </div>
                    <div class="card-body">
                        <form id="resolve-claim-form" action="{{ route('inspection-warranty.claims.resolve', $claim->id) }}" method="POST">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Decision') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="claim_status" required>
                                    <option value="approved">{{ __('Approve') }}</option>
                                    <option value="rejected">{{ __('Reject') }}</option>
                                    <option value="resolved">{{ __('Mark as Resolved') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Decision Outcome') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="decision_outcome" id="decision_outcome" required>
                                    <option value="full_refund">{{ __('Full Refund') }}</option>
                                    <option value="partial_refund">{{ __('Partial Refund') }}</option>
                                    <option value="repair">{{ __('Repair') }}</option>
                                    <option value="replacement">{{ __('Replacement') }}</option>
                                    <option value="rejected">{{ __('Reject Claim') }}</option>
                                    <option value="no_action">{{ __('No Action Required') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3" id="refund_amount_group" style="display: none;">
                                <label class="form-label">{{ __('Refund Amount') }}</label>
                                <input type="number" class="form-control" name="refund_amount" 
                                    step="0.01" min="0" placeholder="0.00">
                                <small class="form-text text-muted">{{ __('Enter refund amount if applicable') }}</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Admin Response') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="admin_response" rows="5" required
                                    placeholder="{{ __('Enter detailed admin response and decision explanation...') }}">{{ $claim->admin_response }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> {{ __('Resolve Claim') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Show Resolution Details -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Resolution Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{ __('Decision Outcome') }}:</strong>
                            <p>
                                @if($claim->decision_outcome === 'full_refund')
                                    <span class="badge bg-success">Full Refund</span>
                                @elseif($claim->decision_outcome === 'partial_refund')
                                    <span class="badge bg-info">Partial Refund</span>
                                @elseif($claim->decision_outcome === 'repair')
                                    <span class="badge bg-primary">Repair</span>
                                @elseif($claim->decision_outcome === 'replacement')
                                    <span class="badge bg-warning">Replacement</span>
                                @elseif($claim->decision_outcome === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">No Action</span>
                                @endif
                            </p>
                        </div>
                        @if($claim->refund_amount)
                            <div class="mb-3">
                                <strong>{{ __('Refund Amount') }}:</strong>
                                <p class="text-success">{{ number_format($claim->refund_amount, 2) }}</p>
                            </div>
                        @endif
                        <div class="mb-3">
                            <strong>{{ __('Admin Response') }}:</strong>
                            <p>{{ $claim->admin_response }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
document.getElementById('decision_outcome').addEventListener('change', function() {
    const refundOptions = ['full_refund', 'partial_refund'];
    const refundGroup = document.getElementById('refund_amount_group');
    
    if (refundOptions.includes(this.value)) {
        refundGroup.style.display = 'block';
    } else {
        refundGroup.style.display = 'none';
    }
});

document.getElementById('resolve-claim-form')?.addEventListener('submit', function(e) {
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
            showToast('success', data.message || 'Claim resolved successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Failed to resolve claim');
        }
    });
});
</script>
@endsection
