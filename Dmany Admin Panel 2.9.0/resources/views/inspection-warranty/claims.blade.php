@extends('layouts.main')

@section('title')
    {{ __('Warranty Claims') }}
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
            <h5 class="card-title">{{ __('Warranty Claims & Disputes') }}</h5>
            <p class="text-muted">{{ __('Manage warranty claims and resolve disputes') }}</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter_status">{{ __('Status Filter') }}</label>
                    <select class="form-control" id="filter_status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="under_review">{{ __('Under Review') }}</option>
                        <option value="approved">{{ __('Approved') }}</option>
                        <option value="rejected">{{ __('Rejected') }}</option>
                        <option value="resolved">{{ __('Resolved') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>

            <table class="table-borderless table-striped" id="claims_table"
                data-toggle="table" data-url="{{ route('inspection-warranty.claims.data') }}"
                data-side-pagination="server" data-pagination="true"
                data-page-list="[5, 10, 20, 50, 100]" data-search="false"
                data-show-refresh="true" data-responsive="true"
                data-sort-name="created_at" data-sort-order="desc"
                data-query-params="claimsQueryParams">
                <thead>
                    <tr>
                        <th data-field="claim_number" data-sortable="true">{{ __('Claim Number') }}</th>
                        <th data-field="order_number" data-sortable="true">{{ __('Order Number') }}</th>
                        <th data-field="item_name" data-sortable="true">{{ __('Product') }}</th>
                        <th data-field="user_name" data-sortable="true">{{ __('Buyer') }}</th>
                        <th data-field="description" data-formatter="descriptionFormatter">{{ __('Description') }}</th>
                        <th data-field="status" data-sortable="true" data-formatter="statusFormatter">{{ __('Status') }}</th>
                        <th data-field="decision_outcome" data-formatter="outcomeFormatter">{{ __('Outcome') }}</th>
                        <th data-field="refund_amount">{{ __('Refund Amount') }}</th>
                        <th data-field="resolved_by">{{ __('Resolved By') }}</th>
                        <th data-field="created_at" data-sortable="true">{{ __('Created') }}</th>
                        <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">{{ __('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<script>
function claimsQueryParams(params) {
    params.status = $('#filter_status').val();
    return params;
}

$('#filter_status').on('change', function() {
    $('#claims_table').bootstrapTable('refresh');
});

function descriptionFormatter(value, row) {
    if (!value) return '<span class="text-muted">-</span>';
    return value.length > 50 ? value.substring(0, 50) + '...' : value;
}

function statusFormatter(value, row) {
    const statusMap = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'under_review': '<span class="badge bg-info">Under Review</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>',
        'resolved': '<span class="badge bg-success">Resolved</span>',
        'cancelled': '<span class="badge bg-dark">Cancelled</span>',
    };
    return statusMap[value] || value;
}

function outcomeFormatter(value, row) {
    if (!value) return '<span class="text-muted">-</span>';
    const outcomeMap = {
        'full_refund': '<span class="badge bg-success">Full Refund</span>',
        'partial_refund': '<span class="badge bg-info">Partial Refund</span>',
        'repair': '<span class="badge bg-primary">Repair</span>',
        'replacement': '<span class="badge bg-warning">Replacement</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>',
        'no_action': '<span class="badge bg-secondary">No Action</span>',
    };
    return outcomeMap[value] || value;
}

function operateFormatter(value, row) {
    const detailUrl = "{{ route('inspection-warranty.claims.detail', ':id') }}".replace(':id', row.id);
    return `
        <a href="${detailUrl}" class="btn btn-sm btn-primary">
            <i class="bi bi-eye"></i> {{ __('View & Resolve') }}
        </a>
    `;
}
</script>
@endsection
