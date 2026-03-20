<x-layouts::auth :title="__('Inloggen')">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl mb-2 text-amber-900 font-medium">MakersMarkt</h1>
            <p class="text-gray-600">Welkom terug!</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
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
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    required
                    autocomplete="current-password"
                />
            </div>

            <!-- Onthoud mij -->
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-sm text-gray-600">Onthoud mij</label>
            </div>

            <!-- Foutmelding -->
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">
                    Onjuiste gebruikersnaam of wachtwoord.
                </div>
            @endif

            <button
                type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-lg font-medium transition-colors"
            >
                Inloggen
            </button>
        </form>

        @if (Route::has('register'))
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Nog geen account?
                    <a href="{{ route('register') }}" class="text-amber-600 hover:text-amber-700 font-medium" wire:navigate>
                        Registreer hier
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-layouts::auth>

</x-layouts::auth>
