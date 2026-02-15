@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="row g-4 mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold text-primary mb-1" style="font-size: 0.75rem;">Total Service Seekers</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $total_seekers }}</div>
                        </div>
                        <i class="bi bi-search fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                             <div class="text-uppercase fw-bold text-success mb-1" style="font-size: 0.75rem;">Total Service Providers</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $total_residents }}</div>
                        </div>
                        <i class="bi bi-house-door fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Verified Providers Card -->
        <div class="col-md-3">
            <a href="{{ route('admin.users.index', ['role' => 'verified_provider']) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase fw-bold text-info mb-1" style="font-size: 0.75rem;">Verified Providers</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $total_verified_providers }}</div>
                            </div>
                            <i class="bi bi-patch-check fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold text-warning mb-1" style="font-size: 0.75rem;">Services Offered</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $total_services }}</div>
                        </div>
                        <i class="bi bi-tools fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-muted fw-bold text-uppercase small">User Growth</h6>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-muted"
                        id="userRangeFilter" style="font-size: 0.75rem;">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 150px;">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-muted fw-bold text-uppercase small">User Roles</h6>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-muted"
                        id="roleRangeFilter" style="font-size: 0.75rem;">
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="year">Last Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 140px; width: 70%; margin: 0 auto;">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-muted fw-bold text-uppercase small">Request Status</h6>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-muted"
                        id="requestRangeFilter" style="font-size: 0.75rem;">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 150px;">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skill Distribution (Existing) -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 text-muted fw-bold text-uppercase small">Top Categories (All Time)</h6>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 140px; width: 70%; margin: 0 auto;">
                        <canvas id="skillCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users (Existing) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 text-muted fw-bold text-uppercase small">Recent Users</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Username</th>
                                <th class="border-0">Role</th>
                                <th class="border-0">Joined Date</th>
                                <th class="border-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_users as $user)
                                <tr>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        @php
                                            $roleDisplay = match($user->role) {
                                                'resident' => 'Service Provider',
                                                'seeker' => 'Service Seeker',
                                                default => ucfirst($user->role)
                                            };
                                        @endphp
                                        <span class="badge bg-secondary">{{ $roleDisplay }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary py-0">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Minimalist Options - Corporate Style
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 4,
                        displayColors: false,
                        titleFont: { size: 13 },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            display: true, // Visible Labels
                            color: '#94a3b8',
                            font: { size: 10 }
                        }
                    },
                    y: {
                        display: true, // Visible Y-Axis for data readability
                        grid: {
                            display: true,
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 10 },
                            stepSize: 1
                        },
                        min: 0
                    }
                },
                elements: {
                    point: {
                        radius: 3, // Visible points
                        backgroundColor: '#fff',
                        borderWidth: 2,
                        hitRadius: 20,
                        hoverRadius: 5
                    },
                    line: {
                        borderWidth: 2,
                        tension: 0.3
                    }
                },
                layout: {
                    padding: { left: 0, right: 0, top: 10, bottom: 0 }
                }
            };

            const circularOptions = {
                ...commonOptions,
                scales: { x: { display: false }, y: { display: false } },
                cutout: '80%', // Very thin modern ring
                plugins: {
                    legend: {
                        display: true, // Show legend for circular charts as labels can't be on axes
                        position: 'right',
                        labels: {
                            boxWidth: 8,
                            usePointStyle: true,
                            font: { size: 10 },
                            color: '#64748b'
                        }
                    }
                }
            };

            let charts = {};

            // 1. User Growth Chart - Corporate Blue
            const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
            charts.userGrowth = new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: @json($monthly_users['labels']),
                    datasets: [{
                        label: 'New Users',
                        data: @json($monthly_users['data']),
                        borderColor: '#2563eb', // Corporate Blue
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                    }]
                },
                options: commonOptions
            });

            // 2. Role Distribution Chart - Professional Palette
            const roleCtx = document.getElementById('roleDistributionChart').getContext('2d');
            charts.roleDist = new Chart(roleCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($role_stats['labels']),
                    datasets: [{
                        data: @json($role_stats['data']),
                        backgroundColor: [
                            '#0f172a', // Slate 900
                            '#3b82f6', // Blue 500
                            '#94a3b8', // Slate 400
                            '#e2e8f0'  // Slate 200
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: circularOptions
            });

            // 3. Request Status Chart - Status Colors (Muted)
            const requestCtx = document.getElementById('requestStatusChart').getContext('2d');
            charts.requestStatus = new Chart(requestCtx, {
                type: 'bar',
                data: {
                    labels: @json($request_stats['labels']),
                    datasets: [{
                        label: 'Requests',
                        data: @json($request_stats['data']),
                        backgroundColor: [
                            '#f59e0b', // Amber (Pending)
                            '#10b981', // Emerald (Completed)
                            '#ef4444', // Red (Cancelled)
                            '#3b82f6'  // Blue (Other)
                        ],
                        borderRadius: 2,
                        barPercentage: 0.5
                    }]
                },
                options: commonOptions
            });

            // 4. Skill Category Chart
            const skillCtx = document.getElementById('skillCategoryChart').getContext('2d');

            const skillData = @json($skill_dist);
            const skillLabels = skillData.map(item => item.name);
            const skillCounts = skillData.map(item => item.count);

            charts.skillCat = new Chart(skillCtx, {
                type: 'polarArea',
                data: {
                    labels: skillLabels,
                    datasets: [{
                        data: skillCounts,
                        backgroundColor: [
                            '#1e293b',
                            '#334155',
                            '#475569',
                            '#64748b',
                            '#94a3b8'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...circularOptions,
                    plugins: { legend: { display: false } }, // Hide legend for polar to save space, uses radial scale usually but we hid it. Let's show tooltip only.
                    scales: { r: { display: false } }
                }
            });

            // Polling Function
            function updateDashboard() {
                const userRange = document.getElementById('userRangeFilter').value;
                const roleRange = document.getElementById('roleRangeFilter').value;
                const requestRange = document.getElementById('requestRangeFilter').value;

                // Build query params
                const params = new URLSearchParams({
                    user_range: userRange,
                    role_range: roleRange,
                    request_range: requestRange
                });

                fetch('{{ route("admin.stats.api") }}?' + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        charts.userGrowth.data.labels = data.monthly_users.labels;
                        charts.userGrowth.data.datasets[0].data = data.monthly_users.data;
                        charts.userGrowth.update('none');

                        charts.roleDist.data.labels = data.role_stats.labels;
                        charts.roleDist.data.datasets[0].data = data.role_stats.data;
                        charts.roleDist.update('none');

                        charts.requestStatus.data.labels = data.request_stats.labels;
                        charts.requestStatus.data.datasets[0].data = data.request_stats.data;
                        charts.requestStatus.update('none');

                        const newSkillLabels = data.skill_dist.map(item => item.name);
                        const newSkillCounts = data.skill_dist.map(item => item.count);
                        charts.skillCat.data.labels = newSkillLabels;
                        charts.skillCat.data.datasets[0].data = newSkillCounts;
                        charts.skillCat.update('none');
                    })
                    .catch(error => console.error('Error fetching dashboard stats:', error));
            }

            // Listeners
            document.getElementById('userRangeFilter').addEventListener('change', updateDashboard);
            document.getElementById('roleRangeFilter').addEventListener('change', updateDashboard);
            document.getElementById('requestRangeFilter').addEventListener('change', updateDashboard);

            setInterval(updateDashboard, 5000);
        });
    </script>
@endpush


