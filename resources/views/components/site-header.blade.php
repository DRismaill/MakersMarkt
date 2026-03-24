<header class="border-b border-zinc-200 bg-white">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 lg:px-6">
        <!-- Logo -->
        <a href="{{ route('home') }}" wire:navigate class="text-2xl font-bold tracking-tight text-amber-700">
            MakersMarkt
        </a>

        <!-- Navigation Menu -->
        <nav class="flex items-center gap-8 text-zinc-700">
            <!-- Catalogus Link -->
            <a
                href="{{ route('products.index') }}"
                wire:navigate
                class="flex items-center gap-2 text-base font-medium hover:text-amber-700 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5L12 3l8.25 4.5m-16.5 0L12 12m-8.25-4.5v9L12 21m0-9 8.25-4.5m-8.25 4.5v9m8.25-13.5v9L12 21" />
                </svg>
                <span>Catalogus</span>
            </a>

            @auth
                <!-- Portfolio Link (Only for Makers) -->
                @if(auth()->user()->role->value === 'maker')
                <a
                    href="{{ route('products.portfolio') }}"
                    wire:navigate
                    class="flex items-center gap-2 text-base font-medium hover:text-amber-700 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6h1.5m-1.5 3h1.5m-1.5 3h1.5M3 20.25a3 3 0 013-3h10.5a3 3 0 013 3v1.68a1.5 1.5 0 01-1.5 1.5H5a1.5 1.5 0 01-1.5-1.5v-1.68zM7 10.5a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" />
                    </svg>
                    <span>Mijn Portfolio</span>
                </a>
                @endif

                <!-- User Info & Logout -->
                <div class="flex items-center gap-4 border-l border-zinc-200 pl-8">
                    <div class="text-right">
                        <div class="text-sm font-medium text-zinc-900">{{ auth()->user()->username }}</div>
                        <div class="text-xs text-amber-600 font-semibold">Credits: €{{ number_format((float) auth()->user()->credit_balance, 2) }}</div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="text-zinc-700 hover:text-red-600 transition p-2"
                            aria-label="Uitloggen"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v1.5A2.25 2.25 0 0113.5 21h-6a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 017.5 3h6a2.25 2.25 0 012.25 2.25v1.5M9 12h12m0 0l-3.75-3.75M21 12l-3.75 3.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <!-- Login Link (for non-authenticated users) -->
                @if (Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="text-base font-medium text-zinc-700 hover:text-amber-700 transition border-l border-zinc-200 pl-8"
                    >
                        Inloggen
                    </a>
                @endif
            @endauth
        </nav>
    </div>
</header>
