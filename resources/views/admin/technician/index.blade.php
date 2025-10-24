@extends('layouts.app')

@section('title', 'Technicians')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Technicians</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Technician
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

                <form action="{{ route('admin.technician.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name, email, or mobile">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>Enabled</option>
                            <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.technician.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Job Role</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($technicians as $technician)
                            <tr>
                                <td>{{ $technician->id }}</td>
                                <td>
                                    @if($technician->profile_photo)
                                        <img src="{{ asset($technician->profile_photo) }}" alt="Photo" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-person-circle" style="font-size: 1.5rem; color: #ccc;"></i>
                                    @endif
                                </td>
                                <td>{{ $technician->first_name }} {{ $technician->last_name }}</td>
                                <td>{{ $technician->email }}</td>
                                <td>{{ $technician->mobile_number }}</td>
                                <td>{{ $technician->job_role }}</td>
                                <td>
                                    @switch($technician->availability_status)
                                        @case('available')
                                            <span class="badge bg-success">Available</span>
                                            @break
                                        @case('busy')
                                            <span class="badge bg-warning">Busy</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-secondary">Inactive</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if ($technician->status === 'enabled')
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
                                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="{{ $technician->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning toggle-btn"
                                        data-bs-toggle="modal" data-bs-target="#toggleModal"
                                        data-id="{{ $technician->id }}"
                                        data-current="{{ $technician->status }}">
                                        {{ $technician->status === 'enabled' ? 'Disable' : 'Enable' }}
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="deleteTechnician({{ $technician->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $technician->id }}"
                                        action="{{ route('admin.technician.destroy', $technician->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No technicians found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($technicians->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $technicians->withQueryString()->links() }}
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
                    <h5 class="modal-title" id="createModalLabel">Add Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.technician.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name_create" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name_create" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email_create" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile_number_create" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="job_role_create" class="form-label">Job Role <span class="text-danger">*</span></label>
                            <input type="text" name="job_role" id="job_role_create" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="profile_photo_create" class="form-label">Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile_photo_create" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_create" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="availability_status_create" class="form-label">Availability Status <span class="text-danger">*</span></label>
                            <select name="availability_status" id="availability_status_create" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="busy">Busy</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
                        <button type="submit" class="btn btn-primary">Save Technician</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="{{ route('admin.technician.update', ':id') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name_edit" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name_edit" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email_edit" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile_number_edit" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="job_role_edit" class="form-label">Job Role <span class="text-danger">*</span></label>
                            <input type="text" name="job_role" id="job_role_edit" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="profile_photo_edit" class="form-label">Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile_photo_edit" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_edit" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                                <small class="text-muted">Leave empty to keep current photo</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="availability_status_edit" class="form-label">Availability Status <span class="text-danger">*</span></label>
                            <select name="availability_status" id="availability_status_edit" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="busy">Busy</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
                        <button type="submit" class="btn btn-primary">Update Technician</button>
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
                    <p id="toggleText">Are you sure you want to toggle the status of this technician?</p>
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
        previewImage('profile_photo_create', 'preview_create');
        previewImage('profile_photo_edit', 'preview_edit');

        // Edit Modal Population via AJAX
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();

                // Fetch technician data
                fetch(`/admin/technician/${id}/edit`)
                    .then(response => response.json())
                    .then(function(technicianData) {
                        document.getElementById('first_name_edit').value = technicianData.first_name;
                        document.getElementById('last_name_edit').value = technicianData.last_name;
                        document.getElementById('email_edit').value = technicianData.email;
                        document.getElementById('mobile_number_edit').value = technicianData.mobile_number;
                        document.getElementById('job_role_edit').value = technicianData.job_role;
                        document.getElementById('availability_status_edit').value = technicianData.availability_status;
                        document.getElementById('status_edit').value = technicianData.status;

                        // Handle existing photo preview
                        const preview = document.getElementById('preview_edit');
                        if (technicianData.profile_photo) {
                            preview.src = `{{ asset('') }}${technicianData.profile_photo}`;
                            preview.classList.remove('d-none');
                        } else {
                            preview.classList.add('d-none');
                        }

                        const form = document.getElementById('editForm');
                        form.action = form.action.replace(':id', id);
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('Error loading technician data.');
                        modal.hide();
                    });
            });
        });

        // Toggle Modal Population
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const current = this.dataset.current;
                const action = current === 'enabled' ? 'disable' : 'enable';

                document.getElementById('toggleModalLabel').textContent = `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`;
                document.getElementById('toggleText').textContent = `Are you sure you want to ${action} this technician?`;

                const confirmLink = document.getElementById('confirmToggle');
                confirmLink.href = `/admin/technicians/toggle/${id}`;
            });
        });

        // Delete Confirmation
        function deleteTechnician(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This technician will be deleted!",
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

        // Reset Edit Form on Modal Close
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('editForm');
            form.action = form.action.replace(/\/\d+$/, '/:id');
            document.getElementById('profile_photo_edit').value = '';
            const preview = document.getElementById('preview_edit');
            preview.classList.add('d-none');
        });

        // Reset Create Modal on Close
        const createModal = document.getElementById('createModal');
        createModal.addEventListener('hidden.bs.modal', function () {
            this.querySelector('form').reset();
            const preview = document.getElementById('preview_create');
            preview.classList.add('d-none');
        });
    </script>
@endsection