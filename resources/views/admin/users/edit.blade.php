@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white d-flex align-items-center">
            <i class="bi bi-pencil-square me-2"></i>
            <h4 class="mb-0 text-white">Edit User</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.users.form')

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
