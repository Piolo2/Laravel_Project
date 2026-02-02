@extends('admin.layout')

@section('title', 'User Reports')

@section('content')
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i>
        Back to Reports</a>

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">User Role Distribution</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Total Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = $roles_distribution->sum('total'); @endphp
                    @foreach($roles_distribution as $role)
                        <tr>
                            <td>{{ ucfirst($role->role) }}</td>
                            <td>{{ $role->total }}</td>
                            <td>{{ $total > 0 ? round(($role->total / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection