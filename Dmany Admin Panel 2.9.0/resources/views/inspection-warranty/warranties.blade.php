@extends('layouts.main')

@section('title')
    {{ __('Warranty Management') }}
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
            <h5 class="card-title">{{ __('Warranty Management') }}</h5>
            <p class="text-muted">{{ __('View active and expired warranties') }}</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="btn-active" data-status="active">
                            {{ __('Active Warranties') }}
                        </button>
                        <button type="button" class="btn btn-secondary" id="btn-expired" data-status="expired">
                            {{ __('Expired Warranties') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-all" data-status="all">
                            {{ __('All Warranties') }}
                        </button>
                    </div>
                </div>
            </div>

            <table class="table-borderless table-striped" id="warranties_table"
                data-toggle="table" data-url="{{ route('inspection-warranty.warranties.data') }}"
                data-side-pagination="server" data-pagination="true"
                data-page-list="[5, 10, 20, 50, 100]" data-search="false"
                data-show-refresh="true" data-responsive="true"
                data-sort-name="warranty_end_date" data-sort-order="asc"
                data-query-params="warrantiesQueryParams">
                <thead>
                    <tr>
                        <th data-field="order_number" data-sortable="true">{{ __('Order Number') }}</th>
                        <th data-field="item_name" data-sortable="true">{{ __('Product') }}</th>
                        <th data-field="buyer_name" data-sortable="true">{{ __('Buyer') }}</th>
                        <th data-field="warranty_start_date" data-sortable="true">{{ __('Start Date') }}</th>
                        <th data-field="warranty_end_date" data-sortable="true">{{ __('End Date') }}</th>
                        <th data-field="remaining_days" data-sortable="true" data-formatter="daysFormatter">{{ __('Remaining Days') }}</th>
                        <th data-field="status" data-sortable="true" data-formatter="statusFormatter">{{ __('Status') }}</th>
                        <th data-field="condition_score">{{ __('Condition Score') }}</th>
                        <th data-field="grade" data-formatter="gradeFormatter">{{ __('Grade') }}</th>
                        <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">{{ __('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<script>
let currentStatus = 'active';

function warrantiesQueryParams(params) {
    params.status = currentStatus;
    return params;
}

$('#btn-active, #btn-expired, #btn-all').on('click', function() {
    currentStatus = $(this).data('status');
    $(this).addClass('active').siblings().removeClass('active');
    $('#warranties_table').bootstrapTable('refresh');
});

function daysFormatter(value, row) {
    if (value === null) return '<span class="text-muted">-</span>';
    if (value <= 0) return '<span class="text-danger">Expired</span>';
    if (value <= 3) return `<span class="text-warning">${value} days</span>`;
    return `<span class="text-success">${value} days</span>`;
}

function statusFormatter(value, row) {
    if (value === 'warranty_active') {
        return '<span class="badge bg-success">Active</span>';
    }
    return '<span class="badge bg-secondary">Expired</span>';
}

function gradeFormatter(value, row) {
    if (!value) return '<span class="text-muted">-</span>';
    const gradeMap = {
        'A': '<span class="badge bg-success">A</span>',
        'B': '<span class="badge bg-info">B</span>',
        'C': '<span class="badge bg-warning">C</span>',
        'D': '<span class="badge bg-danger">D</span>',
        'Fail': '<span class="badge bg-dark">Fail</span>',
    };
    return gradeMap[value] || value;
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
