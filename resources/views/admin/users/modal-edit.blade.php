<form id="userEditForm" action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')
    
    <!-- Name Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="modal_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
            <input type="text" name="first_name" id="modal_first_name" value="{{ old('first_name', $user->first_name) }}" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('first_name') border-red-500 @enderror" required>
            @error('first_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="modal_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
            <input type="text" name="last_name" id="modal_last_name" value="{{ old('last_name', $user->last_name) }}" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('last_name') border-red-500 @enderror" required>
            @error('last_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Email and Phone -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="modal_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
            <input type="email" name="email" id="modal_email" value="{{ old('email', $user->email) }}" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('email') border-red-500 @enderror" required>
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="modal_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
            <input type="tel" name="phone" id="modal_phone" value="{{ old('phone', $user->phone) }}" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror">
            @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Password Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="modal_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
            <input type="password" name="password" id="modal_password" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('password') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to keep current password</p>
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="modal_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="modal_password_confirmation" 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
        </div>
    </div>

    <!-- Role and Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="modal_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role *</label>
            <select name="role" id="modal_role" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('role') border-red-500 @enderror" required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="branch_manager" {{ old('role', $user->role) === 'branch_manager' ? 'selected' : '' }}>Branch Manager</option>
                <option value="assistant_branch_manager" {{ old('role', $user->role) === 'assistant_branch_manager' ? 'selected' : '' }}>Assistant Branch Manager</option>
                <option value="operation_officer" {{ old('role', $user->role) === 'operation_officer' ? 'selected' : '' }}>Operations Officer</option>
                <option value="headquarters" {{ old('role', $user->role) === 'headquarters' ? 'selected' : '' }}>Headquarters</option>
                <option value="iam" {{ old('role', $user->role) === 'iam' ? 'selected' : '' }}>Identity & Access Management</option>
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="modal_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
            <select name="status" id="modal_status" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                <option value="">Select Status</option>
                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Branch -->
    <div>
        <label for="modal_branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch</label>
        <select name="branch_id" id="modal_branch_id" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('branch_id') border-red-500 @enderror">
            <option value="">No Branch Assigned</option>
            @foreach($branches ?? [] as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }} ({{ $branch->code }})
                </option>
            @endforeach
        </select>
        @error('branch_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Bio -->
    <div>
        <label for="modal_bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
        <textarea name="bio" id="modal_bio" rows="3" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror" 
                  placeholder="Tell us about this user...">{{ old('bio', $user->bio) }}</textarea>
        @error('bio')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeUserEditModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
            Update User
        </button>
    </div>
</form>

