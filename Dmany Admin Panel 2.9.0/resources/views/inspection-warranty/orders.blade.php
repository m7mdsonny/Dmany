@extends('layouts.main')

@section('title')
    {{ __('Inspection Orders') }}
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
            <h5 class="card-title">{{ __('Inspection Orders Management') }}</h5>
            <p class="text-muted">{{ __('View and manage all inspection orders') }}</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter_status">{{ __('Status Filter') }}</label>
                    <select class="form-control" id="filter_status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="device_received">{{ __('Device Received') }}</option>
                        <option value="under_inspection">{{ __('Under Inspection') }}</option>
                        <option value="passed">{{ __('Passed') }}</option>
                        <option value="failed">{{ __('Failed') }}</option>
                        <option value="delivered">{{ __('Delivered') }}</option>
                        <option value="warranty_active">{{ __('Warranty Active') }}</option>
                        <option value="warranty_expired">{{ __('Warranty Expired') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label for="search">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" placeholder="{{ __('Search by order number, product name, buyer name...') }}">
                </div>
            </div>

            <table class="table-borderless table-striped" id="orders_table"
                data-toggle="table" data-url="{{ route('inspection-warranty.orders.data') }}"
                data-side-pagination="server" data-pagination="true"
                data-page-list="[5, 10, 20, 50, 100]" data-search="false"
                data-show-refresh="true" data-fixed-columns="true"
                data-trim-on-search="false" data-escape="true"
                data-responsive="true" data-sort-name="id" data-sort-order="desc"
                data-query-params="ordersQueryParams">
                <thead>
                    <tr>
                        <th data-field="order_number" data-sortable="true">{{ __('Order Number') }}</th>
                        <th data-field="item_name" data-sortable="true">{{ __('Product') }}</th>
                        <th data-field="buyer_name" data-sortable="true">{{ __('Buyer') }}</th>
                        <th data-field="seller_name" data-sortable="true">{{ __('Seller') }}</th>
                        <th data-field="status" data-sortable="true" data-formatter="statusFormatter">{{ __('Status') }}</th>
                        <th data-field="device_price" data-sortable="true">{{ __('Device Price') }}</th>
                        <th data-field="inspection_fee" data-sortable="true">{{ __('Inspection Fee') }}</th>
                        <th data-field="total_amount" data-sortable="true">{{ __('Total') }}</th>
                        <th data-field="assigned_technician" data-sortable="true">{{ __('Technician') }}</th>
                        <th data-field="final_decision" data-formatter="decisionFormatter">{{ __('Decision') }}</th>
                        <th data-field="created_at" data-sortable="true">{{ __('Created') }}</th>
                        <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">{{ __('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<script>
function ordersQueryParams(params) {
    params.search = $('#search').val();
    params.status = $('#filter_status').val();
    return params;
}

$('#search, #filter_status').on('change keyup', function() {
    $('#orders_table').bootstrapTable('refresh');
});

function statusFormatter(value, row) {
    const statusMap = {
        'pending': '<span class="badge bg-secondary">Pending</span>',
        'device_received': '<span class="badge bg-info">Device Received</span>',
        'under_inspection': '<span class="badge bg-primary">Under Inspection</span>',
        'passed': '<span class="badge bg-success">Passed</span>',
        'failed': '<span class="badge bg-danger">Failed</span>',
        'delivered': '<span class="badge bg-info">Delivered</span>',
        'warranty_active': '<span class="badge bg-success">Warranty Active</span>',
        'warranty_expired': '<span class="badge bg-secondary">Warranty Expired</span>',
        'cancelled': '<span class="badge bg-dark">Cancelled</span>',
    };
    return statusMap[value] || value;
}

function decisionFormatter(value, row) {
    if (!value) return '<span class="text-muted">-</span>';
    if (value === 'pass') {
        return '<span class="badge bg-success">Pass</span>';
    }
    return '<span class="badge bg-danger">Fail</span>';
}

function operateFormatter(value, row) {
    const detailUrl = "{{ route('inspection-warranty.orders.detail', ':id') }}".replace(':id', row.id);
    return `
        <a href="${detailUrl}" class="btn btn-sm btn-primary">
            <i class="bi bi-eye"></i> {{ __('View') }}
        </a>
    `;
}
</script>
@endsection
