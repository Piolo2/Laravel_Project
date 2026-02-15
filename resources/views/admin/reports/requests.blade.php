@extends('layouts.admin')

@section('title', 'Service Request Analysis')

@section('content')
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i>
        Back to Reports</a>

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Request Status Distribution</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($status_distribution as $status)
                        <tr>
                            <td><span class="badge bg-secondary">{{ ucfirst($status->status) }}</span></td>
                            <td>{{ $status->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


