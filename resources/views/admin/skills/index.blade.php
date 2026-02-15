@extends('layouts.admin')

@section('title', 'Skills & Categories')

@section('content')
    <div class="row g-4">
        <!-- Add Forms -->
        <div class="col-lg-4">
            <!-- Add Skill -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Add New Skill</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.skills.store') }}" method="POST">
                        @csrf
                         <div class="mb-3">
                            <label for="skill_name" class="form-label text-muted small fw-bold">SKILL NAME</label>
                            <input type="text" name="name" id="skill_name" class="form-control" placeholder="e.g. Plumbing, Tutor" required>
                        </div>
                        <div class="mb-4">
                            <label for="skill_category" class="form-label text-muted small fw-bold">CATEGORY</label>
                            <select name="category_id" id="skill_category" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg me-2"></i> Add Skill
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add Category -->
            <div class="card bg-light border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 text-secondary">Add Category</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="category_name" class="form-label text-muted small fw-bold">CATEGORY NAME</label>
                            <input type="text" name="name" id="category_name" class="form-control bg-white" placeholder="e.g. Home Services"
                                required>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-folder-plus me-2"></i> Add Category
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Skills List -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Existing Skills</h5>
                    <span class="badge bg-light text-dark">{{ $skills->total() }} Total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($skills as $skill)
                                    <tr>
                                        <td class="ps-4 text-muted">#{{ $skill->id }}</td>
                                        <td class="fw-medium">{{ $skill->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3">
                                                {{ $skill->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editSkillModal{{ $skill->id }}" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('admin.skills.destroy', $skill->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Delete this skill?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editSkillModal{{ $skill->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('admin.skills.update', $skill->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content shadow-sm">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title">Edit Skill</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit_skill_name_{{ $skill->id }}" class="form-label text-muted small fw-bold">Skill
                                                                Name</label>
                                                            <input type="text" name="name" id="edit_skill_name_{{ $skill->id }}" class="form-control"
                                                                value="{{ $skill->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_skill_category_{{ $skill->id }}" class="form-label text-muted small fw-bold">Category</label>
                                                            <select name="category_id" id="edit_skill_category_{{ $skill->id }}" class="form-select" required>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" {{ $skill->category_id == $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($skills->hasPages())
                    <div class="card-footer bg-white border-top-0 py-3">
                        {{ $skills->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
