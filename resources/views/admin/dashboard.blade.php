@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Stats Widgets -->
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stats-card-title">Service Providers</div>
                            <div class="stats-card-value">{{ $total_residents }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stats-card-title">Active Services</div>
                            <div class="stats-card-value">{{ $total_services }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="bi bi-briefcase fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stats-card-title">Registered Seekers</div>
                            <div class="stats-card-value">{{ $total_seekers }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Skill Distribution -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Skill Supply Distribution</h5>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Category</th>
                                    <th class="text-center">Provider Count</th>
                                    <th class="text-end pe-4">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($skill_dist as $row)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $row->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill px-3">{{ $row->count }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <i class="bi bi-arrow-up-right text-success"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">System Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('search') }}"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-map me-2 text-info"></i> Live Map Reference
                        </div>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 text-muted">
                        <div>
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> Monthly Reports
                        </div>
                        <span class="badge bg-secondary">Coming Soon</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 text-muted">
                        <div>
                            <i class="bi bi-shield-lock me-2"></i> Audit Logs
                        </div>
                        <span class="badge bg-success">Active (SQL)</span>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm d-flex align-items-start" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-3 flex-shrink-0"></i>
                <div class="small">
                    <strong>Privacy Notice:</strong><br>Admin access is read-only for sensitive resident data to ensure
                    compliance with privacy regulations.
                </div>
            </div>
        </div>
    </div>
@endsection



