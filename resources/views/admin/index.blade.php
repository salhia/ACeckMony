@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">
    @if(isset($stats))
        <!-- Date Range Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="row align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $stats['date_range']['start'] }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $stats['date_range']['end'] }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="filter" class="icon-sm me-2"></i>
                                    Filter Data
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i data-feather="refresh-cw" class="icon-sm me-2"></i>
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2">Welcome to Admin Dashboard</h4>
                                <p class="text-muted mb-0">
                                    <i data-feather="users" class="icon-sm me-2"></i>
                                    Total Agents: {{ $stats['total_agents'] }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i data-feather="calendar" class="icon-sm me-2"></i>
                                    Period: {{ $stats['date_range']['start'] }} to {{ $stats['date_range']['end'] }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('admin.reports') }}" class="btn btn-primary">
                                    <i data-feather="file-text" class="icon-sm me-2"></i>
                                    View Detailed Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <h5>Total Statistics</h5>
            </div>

            @foreach(['sent', 'received', 'commission', 'transactions'] as $stat)
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-0">{{ $stats['sendRegion'][$stat]['label'] ?? '' }}</h6>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="mb-2">{{ number_format($stats['sendRegion'][$stat]['total'] ?? 0, 2) }}</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success">
                                            <span>Today: {{ number_format($stats['sendRegion'][$stat]['today'] ?? 0, 2) }}</span>
                                            <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Top Comparisons -->
        <div class="row mb-4">
            <!-- Top States -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Top States by Amount</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>Amount</th>
                                        <th>Transactions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['sendRegion']['top_states'] as $state)
                                        <tr>
                                            <td>{{ $state['name'] }}</td>
                                            <td>{{ number_format($state['total_amount'], 2) }}</td>
                                            <td>{{ $state['total_transactions'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Agents -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Top Agents by Amount</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Agent</th>
                                        <th>Amount</th>
                                        <th>Transactions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['sendRegion']['top_agents'] as $agent)
                                        <tr>
                                            <td>{{ $agent['name'] }}</td>
                                            <td>{{ number_format($agent['total_amount'], 2) }}</td>
                                            <td>{{ $agent['total_transactions'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State-wise Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">State-wise Statistics</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>Total Amount</th>
                                        <th>Transactions</th>
                                        <th>Admin Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['sendRegion']['state_stats'] as $state)
                                        <tr>
                                            <td>{{ $state['name'] }}</td>
                                            <td>{{ number_format($state['total_amount'], 2) }}</td>
                                            <td>{{ $state['total_transactions'] }}</td>
                                            <td>{{ number_format($state['total_admin_fee'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent-wise Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Agent-wise Statistics</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Agent</th>
                                        <th>Total Amount</th>
                                        <th>Transactions</th>
                                        <th>Admin Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['sendRegion']['agent_stats'] as $agent)
                                        <tr>
                                            <td>{{ $agent['name'] }}</td>
                                            <td>{{ number_format($agent['total_amount'], 2) }}</td>
                                            <td>{{ $agent['total_transactions'] }}</td>
                                            <td>{{ number_format($agent['total_admin_fee'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Chart -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Transactions Analysis</h6>
                        <div id="transactionsChart"></div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i data-feather="alert-triangle" class="icon-sm me-2"></i>
            No data available at the moment
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
$(function() {
    // Date range validation
    $('#start_date, #end_date').on('change', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
            if (startDate > endDate) {
                alert('Start date cannot be greater than end date');
                $(this).val('');
            }
        }
    });

    // Initialize Chart
    var options = {
        chart: {
            type: 'area',
            height: 350,
            fontFamily: 'Arial, sans-serif',
            toolbar: {
                show: true
            }
        },
        series: [{
            name: 'Sent Amount',
            data: {!! json_encode($stats['sendRegion']['chart_data']['sent'] ?? []) !!}
        }, {
            name: 'Received Amount',
            data: {!! json_encode($stats['sendRegion']['chart_data']['received'] ?? []) !!}
        }],
        xaxis: {
            categories: {!! json_encode($stats['sendRegion']['chart_data']['dates'] ?? []) !!},
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return new Intl.NumberFormat('en-US').format(value);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(value);
                }
            }
        },
        colors: ['#4CAF50', '#2196F3'],
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        },
        dataLabels: {
            enabled: false
        },
        grid: {
            borderColor: '#f1f1f1',
            row: {
                colors: ['transparent', 'transparent']
            }
        },
        markers: {
            size: 4
        }
    };

    var chart = new ApexCharts(document.querySelector("#transactionsChart"), options);
    chart.render();
});
</script>
@endpush
