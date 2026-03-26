@use('Illuminate\Support\Facades\Storage')
<x-layouts::app>
    <div class="min-h-screen bg-gray-50">

        <!-- Page Header -->
        <div class="bg-white border-b border-gray-200 py-8">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Productcatalogus</h1>
                <p class="text-gray-500">Ontdek unieke handgemaakte producten van getalenteerde makers</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('products.index') }}"
                  class="bg-white border border-gray-200 rounded-xl p-5 mb-8 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <span class="font-semibold text-gray-700">Filters</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Zoeken</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Zoek producten..."
                                   class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        </div>
                    </div>

                    <!-- Product Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Producttype</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                            <option value="">Alle types</option>
                            @foreach(\App\Models\ProductType::all() as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Complexity -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Complexiteit</label>
                        <select name="complexity" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                            <option value="">Alle niveaus</option>
                            @foreach(\App\Enums\ComplexityLevel::cases() as $level)
                                <option value="{{ $level->value }}" {{ request('complexity') === $level->value ? 'selected' : '' }}>
                                    {{ ucfirst($level->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-5 rounded-lg text-sm transition">
                        Toepassen
                    </button>
                    @if(request()->hasAny(['search','type','complexity']))
                        <a href="{{ route('products.index') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-5 rounded-lg text-sm transition">
                            Wis filters
                        </a>
                    @endif
                </div>
            </form>

            <!-- Create button for makers -->
            @if(auth()->check() && auth()->user()->role->value === 'maker')
                <div class="flex justify-end mb-6">
                    <a href="{{ route('products.create') }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-5 rounded-lg flex items-center gap-2 text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nieuw Product
                    </a>
                </div>
            @endif

            <!-- Products Grid -->
            @php
                $filtered = $products
                    ->when(request('search'), fn($c) => $c->filter(fn($p) => str_contains(strtolower($p->name), strtolower(request('search')))))
                    ->when(request('type'),   fn($c) => $c->filter(fn($p) => $p->product_type_id == request('type')))
                    ->when(request('complexity'), fn($c) => $c->filter(fn($p) => $p->complexity->value === request('complexity')));
            @endphp

            @if($filtered->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-lg font-medium">Geen producten gevonden</p>
                    @if(request()->hasAny(['search','type','complexity']))
                        <a href="{{ route('products.index') }}" class="text-orange-500 hover:underline mt-2 inline-block text-sm">Wis filters</a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($filtered as $product)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow group">

                            <!-- Product Image -->
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                     class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-56 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Card Body -->
                            <div class="p-4">
                                <!-- Name + Price -->
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $product->name }}</h3>
                                    <span class="text-orange-500 font-bold text-base ml-3 whitespace-nowrap">
                                        €{{ number_format($product->price_credit, 0) }}
                                    </span>
                                </div>

                                <!-- Description -->
                                <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>

                                <!-- Type badge + Rating row -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- Product type -->
                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded">
                                            {{ $product->productType->name }}
                                        </span>

                                        <!-- Moderation flag -->
                                        @if($product->needs_moderation)
                                            <span class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded">
                                                Gemarkeerd voor moderatie
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Rating -->
                                    @if($product->review_count > 0)
                                        <div class="flex items-center gap-1 text-sm text-gray-500 whitespace-nowrap">
                                            <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.95 2.678c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.065 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                                            </svg>
                                            <span class="font-semibold text-gray-700">{{ number_format($product->average_rating, 1) }}</span>
                                            <span class="text-gray-400">({{ $product->review_count }})</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action buttons -->
                                <div class="mt-4">
                                    @if(auth()->check() && auth()->user()->id === $product->maker_id && auth()->user()->role->value === 'maker')
                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg text-sm transition">
                                            Bewerken
                                        </a>
                                    @else
                                        <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg text-sm transition">
                                            Kopen
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-layouts::app>
