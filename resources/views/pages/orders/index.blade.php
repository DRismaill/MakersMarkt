<x-layouts::app>
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-5 text-gray-900 dark:text-gray-100">Mijn bestellingen</h1>

        <table class="min-w-full bg-white dark:bg-zinc-900 rounded-lg">
            <thead>
                <tr class="w-full bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 text-left">
                    <th class="py-3 px-4 font-semibold text-sm">Product</th>
                    <th class="py-3 px-4 font-semibold text-sm">Maker</th>
                    <th class="py-3 px-4 font-semibold text-sm">Datum</th>
                    <th class="py-3 px-4 font-semibold text-sm">Status</th>
                    <th class="py-3 px-4 font-semibold text-sm">Totaalbedrag</th>
                    <th class="py-3 px-4 font-semibold text-sm">Orderreferentie</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b border-gray-200 dark:border-zinc-700 text-gray-900 dark:text-gray-100">
                        <td class="py-3 px-4">{{ $order->product?->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $order->maker?->username ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $order->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
                        <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $order->status?->value ?? (string) $order->status)) }}</td>
                        <td class="py-3 px-4">{{ number_format((float) $order->price_credit, 2) }}</td>
                        <td class="py-3 px-4">#{{ $order->id }}</td>
                    </tr>
                @empty
                    <tr class="border-b border-gray-200 dark:border-zinc-700">
                        <td colspan="6" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300">Je hebt nog geen bestellingen geplaatst.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts::app>
