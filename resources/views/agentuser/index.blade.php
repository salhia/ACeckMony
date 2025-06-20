@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2">Welcome, {{ Auth::user()->name }} | Role: {{ auth()->user()->role }}</h4>
                                <p class="text-muted mb-0">
                                    <i data-feather="map-pin" class="icon-sm me-2"></i>
                                    Region: {{ $stats['region']['name'] ?? 'Not Specified' }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('agent.reports') }}" class="btn btn-primary">
                                    <i data-feather="file-text" class="icon-sm me-2"></i>
                                    View Detailed Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Region Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <h5>Region Statistics: {{ $stats['region']['name'] ?? 'Not Specified' }}</h5>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">Sent Amount Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ number_format($stats['region']['sent']['today'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">Received Amount Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ number_format($stats['region']['received']['today'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">Commission Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ number_format($stats['region']['commission']['today'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">Total Transactions Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ $stats['region']['transactions']['today'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <h5>My Personal Statistics</h5>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">My Sent Amount Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ number_format($stats['user']['sent']['today'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">My Commission Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ number_format($stats['user']['commission']['today'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-0">My Transactions Today</h6>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="mb-2">{{ $stats['user']['transactions']['today'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    @else
        <div class="alert alert-warning">
            <i data-feather="alert-triangle" class="icon-sm me-2"></i>
            No data available at the moment
        </div>
    @endif

    <!-- Charts Section -->
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

    <!-- Transaction Tables -->
    @if(isset($stats['region']['received_transactions']) || isset($stats['region']['sent_transactions']))
        <div class="row">
            <!-- Received Transactions -->
            @if(isset($stats['region']['received_transactions']))
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Today's Received Transactions</h6>
                            <div class="table-responsive">
                                <table id="receivedTransactions" class="table">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Amount</th>
                                            <th>Sender</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['region']['received_transactions'] as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_code }}</td>
                                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ $transaction->senderCustomer->name ?? 'Unknown' }}</td>
                                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No transactions received today</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Sent Transactions -->
            @if(isset($stats['region']['sent_transactions']))
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Today's Sent Transactions</h6>
                            <div class="table-responsive">
                                <table id="sentTransactions" class="table">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Amount</th>
                                            <th>Sender Agent</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['region']['sent_transactions'] as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_code }}</td>
                                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ $transaction->senderAgent->name ?? 'Unknown' }}</td>
                                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No transactions sent today</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Balance Summary</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Opening Balance: <strong>{{ number_format($opening, 2) }} SSP</strong></li>
                <li class="list-group-item">Deposits (Total): <strong>{{ number_format($deposits, 2) }} SSP</strong></li>
                <li class="list-group-item">Commission (Total): <strong>{{ number_format($commission, 2) }} SSP</strong></li>
                <li class="list-group-item">Refill (Approved): <strong>{{ number_format($refill, 2) }} SSP</strong></li>
                <li class="list-group-item">Bank: <strong>{{ number_format($bank, 2) }} SSP</strong></li>
                <li class="list-group-item">Deductions: <strong>{{ number_format($deductions, 2) }} SSP</strong></li>
                <li class="list-group-item">Closing Balance: <strong>{{ number_format($closing, 2) }} SSP</strong></li>
            </ul>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function() {
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
            data: {!! json_encode($stats['region']['chart_data']['sent'] ?? []) !!}
        }, {
            name: 'Received Amount',
            data: {!! json_encode($stats['region']['chart_data']['received'] ?? []) !!}
        }],
        xaxis: {
            categories: {!! json_encode($stats['region']['chart_data']['dates'] ?? []) !!},
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

    // Initialize DataTables
    ['#receivedTransactions', '#sentTransactions'].forEach(function(tableId) {
        if ($(tableId).length) {
            $(tableId).DataTable({
                "order": [[ 3, "desc" ]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json"
                }
            });
        }
    });
});
</script>
@endpush