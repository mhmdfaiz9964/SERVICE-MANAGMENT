@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Users</h4>
                <a href="{{ route('admin.users.create') }}" class="btn btn-success">Add User</a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name, email, or mobile">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>
                                    @if ($user->status === 'active')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Active
                                        </span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> Inactive
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-question-circle me-1"></i> Unknown
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $user->getRoleNames()->first() }}</td>
                                <td class="text-center">
                                    @if ($user->id != 1)
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deleteUser({{ $user->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <form id="delete-form-{{ $user->id }}"
                                            action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <span class="text-muted">Protected</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($users->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This user will be deleted!",
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
    </script>
@endsection
