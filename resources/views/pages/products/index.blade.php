<x-layouts::app>
    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-2xl font-bold">Products List</h1>
            @if(auth()->check() && auth()->user()->role->value === 'maker')
                <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Product
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="bg-white dark:bg-zinc-900 rounded-lg p-4 mb-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Zoeken (naam of beschrijving)</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ $filters['search'] }}"
                        placeholder="Zoek op naam of beschrijving"
                        class="w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded px-3 py-2"
                    >
                </div>

                <div>
                    <label for="product_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Producttype</label>
                    <select id="product_type_id" name="product_type_id" class="w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 rounded px-3 py-2">
                        <option value="">Alle types</option>
                        @foreach($productTypes as $productType)
                            <option value="{{ $productType->id }}" @selected((string) $filters['product_type_id'] === (string) $productType->id)>
                                {{ $productType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="material" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Materiaal</label>
                    <input
                        id="material"
                        name="material"
                        type="text"
                        value="{{ $filters['material'] }}"
                        placeholder="Bijv. hout"
                        class="w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 rounded px-3 py-2"
                    >
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">Reset</a>
            </div>
        </form>

        @php
            $selectedProductType = $productTypes->firstWhere('id', $filters['product_type_id']);
            $hasActiveFilters = filled($filters['search']) || filled($filters['product_type_id']) || filled($filters['material']);
        @endphp

        @if($hasActiveFilters)
            <div class="mb-4">
                <p class="text-sm font-semibold mb-2">Actieve filters:</p>
                <div class="flex flex-wrap gap-2 text-sm">
                    @if(filled($filters['search']))
                        <span class="bg-gray-200 px-2 py-1 rounded">Zoektekst: {{ $filters['search'] }}</span>
                    @endif
                    @if(filled($filters['product_type_id']) && $selectedProductType)
                        <span class="bg-gray-200 px-2 py-1 rounded">Type: {{ $selectedProductType->name }}</span>
                    @endif
                    @if(filled($filters['material']))
                        <span class="bg-gray-200 px-2 py-1 rounded">Materiaal: {{ $filters['material'] }}</span>
                    @endif
                </div>
            </div>
        @endif

        <table class="min-w-full bg-white dark:bg-zinc-900 rounded-lg">
            <thead>
                <tr class="w-full bg-gray-200 dark:bg-zinc-800 text-gray-600 dark:text-gray-200 text-left">
                    <th class="py-3 px-4 font-semibold text-sm">ID</th>
                    <th class="py-3 px-4 font-semibold text-sm">Name</th>
                    <th class="py-3 px-4 font-semibold text-sm">Description</th>
                    <th class="py-3 px-4 font-semibold text-sm">Type</th>
                    <th class="py-3 px-4 font-semibold text-sm">Material</th>
                    <th class="py-3 px-4 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-gray-200 dark:border-zinc-700 text-gray-900 dark:text-gray-100">
                        <td class="py-3 px-4">{{ $product->id }}</td>
                        <td class="py-3 px-4">{{ $product->name }}</td>
                        <td class="py-3 px-4">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</td>
                        <td class="py-3 px-4">{{ $product->productType?->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $product->material }}</td>
                        <td class="py-3 px-4">
                            @if(auth()->check() && auth()->user()->id === $product->maker_id && auth()->user()->role->value === 'maker')
                                <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-sm">
                                    Edit
                                </a>
                            @else
                                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Buy
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="border-b border-gray-200 dark:border-zinc-700">
                        <td colspan="6" class="py-4 px-4 text-center text-gray-600 dark:text-gray-300">Geen producten gevonden voor de gekozen filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts::app>
