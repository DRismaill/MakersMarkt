<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-site-header />

        <main class="mx-auto w-full max-w-7xl px-4 py-12 lg:px-6">
            <section class="mx-auto max-w-3xl rounded-xl border border-zinc-200 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <h1 class="text-3xl font-semibold text-amber-800 dark:text-amber-500">Welkom bij MakersMarkt</h1>
                <p class="mt-3 text-base text-zinc-600 dark:text-zinc-300">
                    Ontdek unieke handgemaakte producten en beheer je portfolio vanuit één plek.
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            wire:navigate
                            class="rounded-md bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                        >
                            Naar dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                class="rounded-md bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200"
                            >
                                Inloggen
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-md border border-zinc-300 px-5 py-2.5 text-sm font-medium text-zinc-800 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-100 dark:hover:bg-zinc-700"
                            >
                                Registreren
                            </a>
                        @endif
                    @endauth
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>
