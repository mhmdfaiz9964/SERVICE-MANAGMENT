@csrf

<div class="mb-3">
    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
    <input type="text" name="first_name" id="first_name" class="form-control" 
           value="{{ old('first_name', $user->first_name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
    <input type="text" name="last_name" id="last_name" class="form-control" 
           value="{{ old('last_name', $user->last_name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
    <input type="email" name="email" id="email" class="form-control" 
           value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="mobile" class="form-label">Mobile</label>
    <input type="text" name="mobile" id="mobile" class="form-control" 
           value="{{ old('mobile', $user->mobile ?? '') }}">
</div>

<div class="mb-3">
    <label for="password" class="form-label">{{ isset($user) ? 'New Password' : 'Password' }}</label>
    <input type="password" name="password" id="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">{{ isset($user) ? 'Confirm New Password' : 'Confirm Password' }}</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
    <select name="status" id="status" class="form-select" required>
        <option value="active" {{ (old('status', $user->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ (old('status', $user->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<div class="mb-3">
    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
    <select name="role" id="role" class="form-select" required>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ (isset($user) && $user->hasRole($role->name)) ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

