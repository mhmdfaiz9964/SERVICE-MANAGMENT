@extends('layouts.app')

@section('title', 'Services')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Services</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Service
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

                <form action="{{ route('admin.services.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name or description">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>Enabled</option>
                            <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>{{ $service->category ? $service->category->name : 'Uncategorized' }}</td>
                                <td>{{ $service->name }}</td>
                                <td>{{ Str::limit($service->description ?? '', 50) }}</td>
                                <td>${{ number_format($service->base_price, 2) }}</td>
                                <td>{{ $service->duration_minutes }} min</td>
                                <td>
                                    @if ($service->status === 'enabled')
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
                                        data-id="{{ $service->id }}"
                                        data-service-category-id="{{ $service->service_category_id }}"
                                        data-name="{{ $service->name }}"
                                        data-description="{{ $service->description ?? '' }}"
                                        data-base-price="{{ $service->base_price }}"
                                        data-duration-minutes="{{ $service->duration_minutes }}"
                                        data-metadata="{{ $service->metadata ? json_encode($service->metadata) : '' }}"
                                        data-status="{{ $service->status }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning toggle-btn"
                                        data-bs-toggle="modal" data-bs-target="#toggleModal"
                                        data-id="{{ $service->id }}"
                                        data-current="{{ $service->status }}">
                                        {{ $service->status === 'enabled' ? 'Disable' : 'Enable' }}
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="deleteService({{ $service->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $service->id }}"
                                        action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($services->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $services->withQueryString()->links() }}
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
                    <h5 class="modal-title" id="createModalLabel">Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="service_category_id_create" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="service_category_id" id="service_category_id_create" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <label for="base_price_create" class="form-label">Base Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" id="base_price_create" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="duration_minutes_create" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes_create" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="metadata_create" class="form-label">Metadata (JSON)</label>
                            <textarea name="metadata" id="metadata_create" class="form-control" rows="3" placeholder='{"key": "value"}'></textarea>
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
                        <button type="submit" class="btn btn-primary">Save Service</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="{{ route('admin.services.update', ':id') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="service_category_id_edit" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="service_category_id" id="service_category_id_edit" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <label for="base_price_edit" class="form-label">Base Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" id="base_price_edit" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="duration_minutes_edit" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes_edit" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="metadata_edit" class="form-label">Metadata (JSON)</label>
                            <textarea name="metadata" id="metadata_edit" class="form-control" rows="3" placeholder='{"key": "value"}'></textarea>
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
                        <button type="submit" class="btn btn-primary">Update Service</button>
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
                    <p id="toggleText">Are you sure you want to toggle the status of this service?</p>
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
        // Edit Modal Population
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const categoryId = this.dataset.serviceCategoryId;
                const name = this.dataset.name;
                const description = this.dataset.description;
                const basePrice = this.dataset.basePrice;
                const durationMinutes = this.dataset.durationMinutes;
                const metadata = this.dataset.metadata;
                const status = this.dataset.status;

                document.getElementById('service_category_id_edit').value = categoryId;
                document.getElementById('name_edit').value = name;
                document.getElementById('description_edit').value = description;
                document.getElementById('base_price_edit').value = basePrice;
                document.getElementById('duration_minutes_edit').value = durationMinutes;
                document.getElementById('metadata_edit').value = metadata;
                document.getElementById('status_edit').value = status;

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
                document.getElementById('toggleText').textContent = `Are you sure you want to ${action} this service?`;

                const confirmLink = document.getElementById('confirmToggle');
                confirmLink.href = `/admin/services/${id}/toggle`;
            });
        });

        // Delete Confirmation
        function deleteService(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This service will be deleted!",
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

        // Reset Edit Form Action on Modal Close
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('editForm');
            form.action = form.action.replace(/\/\d+$/, '/:id');
        });

        // Reset Create Modal on Close
        const createModal = document.getElementById('createModal');
        createModal.addEventListener('hidden.bs.modal', function () {
            this.querySelector('form').reset();
        });
    </script>
@endsection