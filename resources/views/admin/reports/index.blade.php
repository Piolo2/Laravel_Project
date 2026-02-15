@extends('layouts.admin')

@section('title', 'System Reports')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded text-primary me-3">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                        <h6 class="mb-0 text-muted text-uppercase small fw-bold">Total Users</h6>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $stats['total_users'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded text-info me-3">
                            <i class="bi bi-file-earmark-text fs-5"></i>
                        </div>
                        <h6 class="mb-0 text-muted text-uppercase small fw-bold">Total Requests</h6>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $stats['total_requests'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-warning me-3">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <h6 class="mb-0 text-muted text-uppercase small fw-bold">Pending</h6>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $stats['pending_requests'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded text-success me-3">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                        <h6 class="mb-0 text-muted text-uppercase small fw-bold">Completed</h6>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $stats['completed_requests'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Reports Navigation</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.reports.users') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-lines-fill text-primary me-3 fs-5"></i>
                                <div>
                                    <div class="fw-medium">User Demographics Report</div>
                                    <div class="small text-muted">Analyze user growth and distribution</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.reports.requests') }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-bar-chart-fill text-info me-3 fs-5"></i>
                                <div>
                                    <div class="fw-medium">Service Request Analysis</div>
                                    <div class="small text-muted">Track service demand and completion rates</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Export Data</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Download system data in CSV format for external analysis and record keeping.
                    </p>

                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.reports.export.users') }}"
                            class="btn btn-outline-success p-3 text-start d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bi bi-file-earmark-excel me-2"></i> Export All Users
                            </div>
                            <span class="badge bg-light text-dark">CSV</span>
                        </a>
                        <a href="{{ route('admin.reports.export.requests') }}"
                            class="btn btn-outline-primary p-3 text-start d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Export Request Data
                            </div>
                            <span class="badge bg-light text-dark">CSV</span>
                        </a>
                        <button class="btn btn-primary p-3 text-start d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bi bi-briefcase me-2"></i> Generate Workforce Report
                            </div>
                            <span class="badge bg-light text-primary">PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

