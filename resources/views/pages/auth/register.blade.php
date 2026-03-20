<x-layouts::auth :title="__('Registreren')">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl mb-2 text-amber-900 font-medium">MakersMarkt</h1>
            <p class="text-gray-600">Maak een account aan</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <!-- Gebruikersnaam -->
            <div>
                <label for="username" class="block text-sm font-medium mb-1 text-gray-700">
                    Gebruikersnaam
                </label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    value="{{ old('username') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('username') border-red-400 @enderror"
                    required
                    autofocus
                    autocomplete="username"
                />
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- E-mailadres -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1 text-gray-700">
                    E-mailadres <span class="text-gray-400 font-normal">(optioneel)</span>
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('email') border-red-400 @enderror"
                    autocomplete="email"
                    placeholder="email@voorbeeld.nl"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Wachtwoord -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1 text-gray-700">
                    Wachtwoord
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('password') border-red-400 @enderror"
                    required
                    autocomplete="new-password"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Herhaal wachtwoord -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1 text-gray-700">
                    Herhaal wachtwoord
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    required
                    autocomplete="new-password"
                />
            </div>

            <!-- Rol -->
            <div>
                <label for="role" class="block text-sm font-medium mb-1 text-gray-700">
                    Ik registreer als...
                </label>
                <select
                    id="role"
                    name="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white text-gray-900"
                >
                    <option value="buyer" class="text-gray-900 bg-white" {{ old('role', 'buyer') === 'buyer' ? 'selected' : '' }}>Koper</option>
                    <option value="maker" class="text-gray-900 bg-white" {{ old('role') === 'maker' ? 'selected' : '' }}>Maker</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Algemene foutmeldingen -->
            @if ($errors->has('general'))
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">
                    {{ $errors->first('general') }}
                </div>
            @endif

            <button
                type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-lg font-medium transition-colors"
                data-test="register-user-button"
            >
                Registreren
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Al een account?
                <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-700 font-medium" wire:navigate>
                    Log hier in
                </a>
            </p>
        </div>
    </div>
</x-layouts::auth>

