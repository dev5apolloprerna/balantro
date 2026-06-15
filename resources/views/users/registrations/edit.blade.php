<div class="max-w-md mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Edit Profile</h2>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="email" class="block mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" 
                   class="w-full px-3 py-2 border rounded" required autocomplete="email">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mb-4 text-yellow-600">
                Currently waiting confirmation for: {{ $user->unconfirmed_email }}
            </div>
        @endif

        <div class="mb-4">
            <label for="password" class="block mb-2">New Password <span class="text-sm text-gray-500">(leave blank if you don't want to change it)</span></label>
            <input id="password" type="password" name="password" 
                   class="w-full px-3 py-2 border rounded" autocomplete="new-password">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            @if (config('auth.password_min_length'))
                <em class="text-sm text-gray-500">{{ config('auth.password_min_length') }} characters minimum</em>
            @endif
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block mb-2">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" 
                   class="w-full px-3 py-2 border rounded" autocomplete="new-password">
        </div>

        <div class="mb-6">
            <label for="current_password" class="block mb-2">Current Password <span class="text-sm text-gray-500">(we need your current password to confirm your changes)</span></label>
            <input id="current_password" type="password" name="current_password" 
                   class="w-full px-3 py-2 border rounded" required autocomplete="current-password">
            @error('current_password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update Profile
            </button>
        </div>
    </form>

    <div class="mt-8 border-t pt-6">
        <h3 class="text-xl font-bold mb-4">Delete Account</h3>
        <p class="mb-4">Unhappy with your account?</p>
        
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                Delete My Account
            </button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">Back</a>
    </div>
</div>