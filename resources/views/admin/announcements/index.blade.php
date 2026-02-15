@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Announcements</h1>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Announcement
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Admin Name</th>
                            <th>Posted Date</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr>
                                <td>{{ $announcement->title }}</td>
                                <td>
                                    @if($announcement->image_path)
                                        <img src="{{ asset($announcement->image_path) }}" alt="Img" style="height: 50px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $announcement->admin_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($announcement->date_posted)->format('Y-m-d') }}</td>
                                <td>{{ \Carbon\Carbon::parse($announcement->deadline)->format('Y-m-d') }}</td>
                                <td>
                                    @if(\Carbon\Carbon::parse($announcement->deadline)->isPast())
                                        <span class="badge bg-secondary">Expired</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.announcements.edit', $announcement->id) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No announcements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection