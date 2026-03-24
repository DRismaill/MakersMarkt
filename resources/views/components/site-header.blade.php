<header class="border-b border-zinc-200 bg-zinc-100">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center px-4 lg:px-6">
        <a href="{{ route('home') }}" wire:navigate class="text-2xl font-medium tracking-tight text-amber-800">
            MakersMarkt
        </a>

        <nav class="ms-auto flex h-full items-center text-zinc-700">
            <a
                href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                wire:navigate
                class="flex h-full items-center gap-1.5 px-4 text-base hover:text-zinc-900"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5m-16.5 0L12 12m-8.25-4.5v9L12 21m0-9 8.25-4.5m-8.25 4.5v9m8.25-13.5v9L12 21" />
                </svg>
                <span>Catalogus</span>
            </a>

            <a
                href="{{ auth()->check() ? route('profile.edit') : route('home') }}"
                wire:navigate
                class="flex h-full items-center gap-1.5 border-s border-zinc-300 px-4 text-base hover:text-zinc-900"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5h10.5m-10.5 4.5h10.5m-10.5 4.5h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5h13.5A1.5 1.5 0 0 1 20.25 6v12a1.5 1.5 0 0 1-1.5 1.5H5.25A1.5 1.5 0 0 1 3.75 18V6a1.5 1.5 0 0 1 1.5-1.5Z" />
                </svg>
                <span>Mijn Portfolio</span>
            </a>

            @auth
                <div class="flex h-full items-center border-s border-zinc-300 px-4">
                    <div class="text-right leading-tight">
                        <div class="text-sm text-zinc-700">{{ auth()->user()->username }}</div>
                        <div class="text-sm text-amber-600">Credits: €{{ number_format((float) auth()->user()->credit_balance, 0, ',', '.') }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="flex h-full items-center border-s border-zinc-300 px-4">
                    @csrf
                    <button type="submit" class="text-zinc-700 transition hover:text-zinc-900" data-test="logout-button" aria-label="Uitloggen">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v1.5A2.25 2.25 0 0 1 13.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V5.25A2.25 2.25 0 0 1 7.5 3h6a2.25 2.25 0 0 1 2.25 2.25v1.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h12m0 0-3.75-3.75M21 12l-3.75 3.75" />
                        </svg>
                    </button>
                </form>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="flex h-full items-center border-s border-zinc-300 px-4 text-base hover:text-zinc-900">
                        Inloggen
                    </a>
                @endif
            @endauth
        </nav>
    </div>
</header>
