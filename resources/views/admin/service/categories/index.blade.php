@extends('layouts.app')

@section('title', 'Service Categories')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Service Categories</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Category
                </button>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.service.categories.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>Enabled</option>
                            <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.service.categories.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->icon)
                                        <img src="{{ asset('storage/' . $category->icon) }}" alt="Icon" class="img-thumbnail" style="width: 30px; height: 30px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-image" style="font-size: 1.5rem; color: #ccc;"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($category->parent)
                                        <small class="text-muted">{{ $category->parent->name }}</small> &gt; 
                                    @endif
                                    {{ $category->name }}
                                </td>
                                <td>{{ Str::limit($category->description ?? '', 50) }}</td>
                                <td>
                                    @if ($category->status === 'enabled')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Enabled
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> Disabled
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary edit-btn"
                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-parent-id="{{ $category->parent_id ?? '' }}"
                                        data-description="{{ $category->description ?? '' }}"
                                        data-icon="{{ $category->icon ?? '' }}"
                                        data-status="{{ $category->status }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning toggle-btn"
                                        data-bs-toggle="modal" data-bs-target="#toggleModal"
                                        data-id="{{ $category->id }}"
                                        data-current="{{ $category->status }}">
                                        {{ $category->status === 'enabled' ? 'Disable' : 'Enable' }}
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="deleteCategory({{ $category->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $category->id }}"
                                        action="{{ route('admin.service.categories.destroy', $category->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($categories->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $categories->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Add Service Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.service.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="parent_id_create" class="form-label">Parent Category</label>
                            <select name="parent_id" id="parent_id_create" class="form-select">
                                <option value="">-- No Parent --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name_create" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description_create" class="form-label">Description</label>
                            <textarea name="description" id="description_create" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="icon_create" class="form-label">Icon Image <small>(JPEG, PNG, JPG, GIF, SVG - Max 2MB)</small></label>
                            <input type="file" name="icon" id="icon_create" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_create" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_create" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status_create" class="form-select" required>
                                <option value="enabled">Enabled</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Service Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="{{ route('admin.service.categories.update', ':id') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="parent_id_edit" class="form-label">Parent Category</label>
                            <select name="parent_id" id="parent_id_edit" class="form-select">
                                <option value="">-- No Parent --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name_edit" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description_edit" class="form-label">Description</label>
                            <textarea name="description" id="description_edit" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="icon_edit" class="form-label">Icon Image <small>(JPEG, PNG, JPG, GIF, SVG - Max 2MB) - Leave empty to keep current</small></label>
                            <input type="file" name="icon" id="icon_edit" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_edit" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_edit" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status_edit" class="form-select" required>
                                <option value="enabled">Enabled</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toggle Status Modal --}}
    <div class="modal fade" id="toggleModal" tabindex="-1" aria-labelledby="toggleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="toggleModalLabel">Confirm Toggle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="toggleText">Are you sure you want to toggle the status of this category?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmToggle" href="#" class="btn btn-warning">Yes, Toggle</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Function to handle image preview
        function previewImage(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            input.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('d-none');
                }
            });
        }

        // Initialize previews for create and edit modals
        previewImage('icon_create', 'preview_create');
        previewImage('icon_edit', 'preview_edit');

        // Edit Modal Population
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const parentId = this.dataset.parentId;
                const description = this.dataset.description;
                const icon = this.dataset.icon;
                const status = this.dataset.status;

                document.getElementById('name_edit').value = name;
                document.getElementById('parent_id_edit').value = parentId;
                document.getElementById('description_edit').value = description;
                document.getElementById('icon_edit').value = ''; // Clear file input
                document.getElementById('status_edit').value = status;

                // Handle existing icon preview
                const preview = document.getElementById('preview_edit');
                if (icon) {
                    preview.src = `/storage/${icon}`;
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }

                const form = document.getElementById('editForm');
                form.action = form.action.replace(':id', id);
            });
        });

        // Toggle Modal Population
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.dataset.id;
                const current = this.dataset.current;
                const action = current === 'enabled' ? 'disable' : 'enable';

                document.getElementById('toggleModalLabel').textContent = `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`;
                document.getElementById('toggleText').textContent = `Are you sure you want to ${action} this category?`;

                const confirmLink = document.getElementById('confirmToggle');
                confirmLink.href = `/admin/service/categories/${id}/toggle`; // Adjust to match your route URL
            });
        });

        // Delete Confirmation
        function deleteCategory(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This category will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // Reset Edit Form Action and Preview on Modal Close
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('editForm');
            form.action = form.action.replace(/\/\d+$/, '/:id'); // Reset to placeholder
            document.getElementById('icon_edit').value = ''; // Clear file input
            const preview = document.getElementById('preview_edit');
            preview.classList.add('d-none');
        });

        // Reset Create Preview on Modal Close
        const createModal = document.getElementById('createModal');
        createModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('icon_create').value = '';
            const preview = document.getElementById('preview_create');
            preview.classList.add('d-none');
        });
    </script>
@endsection