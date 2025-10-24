@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Customers</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Customer
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

                <form action="{{ route('admin.customer.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name, email, or mobile">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">Clear</a>
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
                            <th>WhatsApp</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>
                                    @if($customer->profile_pic)
                                        <img src="{{ asset($customer->profile_pic) }}" alt="Photo" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-person-circle" style="font-size: 1.5rem; color: #ccc;"></i>
                                    @endif
                                </td>
                                <td>{{ $customer->full_name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->mobile_number }}</td>
                                <td>{{ $customer->whatsapp_number ?? 'N/A' }}</td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="{{ $customer->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="deleteCustomer({{ $customer->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $customer->id }}"
                                        action="{{ route('admin.customer.destroy', $customer->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($customers->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $customers->withQueryString()->links() }}
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
                    <h5 class="modal-title" id="createModalLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.customer.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="full_name_create" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="full_name_create" class="form-control" required>
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
                            <label for="whatsapp_number_create" class="form-label">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number_create" class="form-control" maxlength="20">
                        </div>

                        <div class="mb-3">
                            <label for="profile_pic_create" class="form-label">Profile Picture</label>
                            <input type="file" name="profile_pic" id="profile_pic_create" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_create" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="{{ route('admin.customer.update', ':id') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="full_name_edit" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="full_name_edit" class="form-control" required>
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
                            <label for="whatsapp_number_edit" class="form-label">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number_edit" class="form-control" maxlength="20">
                        </div>

                        <div class="mb-3">
                            <label for="profile_pic_edit" class="form-label">Profile Picture</label>
                            <input type="file" name="profile_pic" id="profile_pic_edit" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="preview_edit" src="" alt="Preview" class="img-thumbnail d-none" style="width: 100px; height: 100px; object-fit: cover;">
                                <small class="text-muted">Leave empty to keep current photo</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
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
        previewImage('profile_pic_create', 'preview_create');
        previewImage('profile_pic_edit', 'preview_edit');

        // Edit Modal Population via AJAX
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();

                // Fetch customer data
                fetch(`/admin/customer/${id}/edit`)
                    .then(response => response.json())
                    .then(function(customerData) {
                        document.getElementById('full_name_edit').value = customerData.full_name;
                        document.getElementById('email_edit').value = customerData.email;
                        document.getElementById('mobile_number_edit').value = customerData.mobile_number;
                        document.getElementById('whatsapp_number_edit').value = customerData.whatsapp_number || '';

                        // Handle existing photo preview
                        const preview = document.getElementById('preview_edit');
                        if (customerData.profile_pic) {
                            preview.src = `{{ asset('') }}${customerData.profile_pic}`;
                            preview.classList.remove('d-none');
                        } else {
                            preview.classList.add('d-none');
                        }

                        const form = document.getElementById('editForm');
                        form.action = form.action.replace(':id', id);
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('Error loading customer data.');
                        modal.hide();
                    });
            });
        });

        // Delete Confirmation
        function deleteCustomer(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This customer will be deleted!",
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
            document.getElementById('profile_pic_edit').value = '';
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