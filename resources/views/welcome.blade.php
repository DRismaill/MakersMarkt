<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white">
        <x-site-header />

        {{-- Hero Section --}}
        <section class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 py-20 px-4">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="text-4xl font-semibold text-amber-900 md:text-5xl leading-tight">
                    Koop en verkoop unieke producten op MakersMarkt
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Ontdek handgemaakte en gepersonaliseerde producten van gepassioneerde makers. Direct van maker tot koper.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="#"
                        class="rounded-lg bg-amber-600 hover:bg-amber-700 px-6 py-3 text-base font-medium text-white transition-colors"
                    >
                        Bekijk producten
                    </a>
                    @guest
                        <a
                            href="{{ route('register') }}"
                            class="rounded-lg border border-amber-600 px-6 py-3 text-base font-medium text-amber-700 hover:bg-amber-50 transition-colors"
                        >
                            Maak een account
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- About / How it works Section --}}
        <section id="hoe-werkt-het" class="py-16 px-4 bg-amber-50">
            <div class="mx-auto max-w-7xl">
                <div class="text-center mb-12">
                    <h2 class="text-2xl font-semibold text-amber-900 mb-2">Wat is MakersMarkt?</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        MakersMarkt is een platform waar makers en kopers samenkomen. Kopers ontdekken unieke, handgemaakte producten en makers krijgen een plek om hun werk te latten zien en te verkopen.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-14">
                    {{-- Step 1 --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
                        <div class="flex items-center justify-center size-12 rounded-full bg-amber-100 text-amber-700 mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="text-sm font-medium text-amber-600 mb-1">Stap 1</div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Maak een account</h3>
                        <p class="text-sm text-gray-500">Registreer gratis als koper of maker. Kies je rol en ga direct aan de slag.</p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
                        <div class="flex items-center justify-center size-12 rounded-full bg-amber-100 text-amber-700 mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </div>
                        <div class="text-sm font-medium text-amber-600 mb-1">Stap 2</div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Ontdek of verkoop producten</h3>
                        <p class="text-sm text-gray-500">Makers voegen producten toe aan hun portfolio. Kopers bladeren door de catalogus en vinden wat bij hen past.</p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm text-center">
                        <div class="flex items-center justify-center size-12 rounded-full bg-amber-100 text-amber-700 mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </div>
                        <div class="text-sm font-medium text-amber-600 mb-1">Stap 3</div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Koop en verkoop</h3>
                        <p class="text-sm text-gray-500">Gebruik credits om producten te kopen. Makers ontvangen credits die ze weer kunnen inzetten op het platform.</p>
                    </div>
                </div>

                {{-- CTA --}}
                @guest
                    <div class="text-center">
                        <a
                            href="{{ route('register') }}"
                            class="inline-block rounded-lg bg-amber-600 hover:bg-amber-700 px-8 py-3 text-base font-medium text-white transition-colors"
                        >
                            Begin nu gratis
                        </a>
                        <p class="mt-3 text-sm text-gray-500">
                            Al een account?
                            <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-700 font-medium">Log hier in</a>
                        </p>
                    </div>
                @endguest
            </div>
        </section>

        @fluxScripts
    </body>
</html>
