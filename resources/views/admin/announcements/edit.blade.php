@extends('layouts.admin')

@section('title', 'Edit Announcement')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Announcement</h1>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required
                        value="{{ old('title', $announcement->title) }}">
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Update Image (Optional)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    @if($announcement->image_path)
                        <div class="mt-2">
                            <img src="{{ asset($announcement->image_path) }}" alt="Current Image"
                                style="height: 100px; border-radius: 5px;">
                            <p class="text-muted small">Current Image</p>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="admin_name" class="form-label">Admin Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="admin_name" name="admin_name" required
                        value="{{ old('admin_name', $announcement->admin_name) }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        required>{{ old('description', $announcement->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_posted" class="form-label">Date Posted <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_posted" name="date_posted" required
                            value="{{ old('date_posted', $announcement->date_posted) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="deadline" name="deadline" required
                            value="{{ old('deadline', $announcement->deadline) }}">
                        <div class="form-text">Announcement will be automatically hidden after this date.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Announcement</button>
            </form>
        </div>
    </div>
@endsection