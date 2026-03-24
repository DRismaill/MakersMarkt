<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-linear-to-br from-amber-50 via-orange-50 to-yellow-50 antialiased">
        <x-site-header />

        <div class="flex min-h-[calc(100vh-88px)] flex-col items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
