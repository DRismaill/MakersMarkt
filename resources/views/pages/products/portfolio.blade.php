<x-layouts::app>
    <div class="bg-white">
        <!-- Header Section -->
        <div class="border-b border-gray-200 py-8">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Mijn Portfolio</h1>
                <p class="text-gray-600">Beheer je producten en bestellingen</p>
            </div>
        </div>

        <!-- New Orders Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center gap-2 mb-6">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">Nieuwe Bestellingen (0)</h2>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <p class="text-gray-500 text-center">Geen nieuwe bestellingen op dit moment</p>
            </div>
        </div>

        <!-- My Products Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    Mijn Producten ({{ $products->count() }}
                    @if(request()->hasAny(['type','complexity','status','search']))
                        <span class="text-base font-normal text-orange-500">gefilterd</span>
                    @endif)
                </h2>
                <a href="{{ route('products.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nieuw Product
                </a>
            </div>

            @if(!$products->isEmpty() || request()->hasAny(['type','complexity','status','search']))
                <!-- Filter & Sort Bar -->
                <form method="GET" action="{{ route('products.portfolio') }}" class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Zoek op naam</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Productnaam..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            >
                        </div>

                        <!-- Product Type -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Type</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Alle types</option>
                                @foreach ($productTypes as $type)
                                    <option value="{{ $type->id }}" {{ $filterType == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Complexity -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Complexiteit</label>
                            <select name="complexity" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Alle niveaus</option>
                                @foreach ($complexityLevels as $level)
                                    <option value="{{ $level->value }}" {{ $filterComplexity === $level->value ? 'selected' : '' }}>
                                        {{ ucfirst($level->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Approval Status -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Alle statussen</option>
                                <option value="pending"  {{ $filterStatus === 'pending'  ? 'selected' : '' }}>In afwachting</option>
                                <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Goedgekeurd</option>
                                <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>Afgewezen</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex items-center justify-between mt-3 gap-3 flex-wrap">
                        <!-- Sort -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Sorteren op:</label>
                            <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="newest"     {{ $currentSort === 'newest'     ? 'selected' : '' }}>Nieuwste eerst</option>
                                <option value="oldest"     {{ $currentSort === 'oldest'     ? 'selected' : '' }}>Oudste eerst</option>
                                <option value="name_asc"   {{ $currentSort === 'name_asc'   ? 'selected' : '' }}>Naam (A → Z)</option>
                                <option value="name_desc"  {{ $currentSort === 'name_desc'  ? 'selected' : '' }}>Naam (Z → A)</option>
                                <option value="price_asc"  {{ $currentSort === 'price_asc'  ? 'selected' : '' }}>Prijs (Laag → Hoog)</option>
                                <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Prijs (Hoog → Laag)</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                                Toepassen
                            </button>
                            @if(request()->hasAny(['type','complexity','status','search']))
                                <a href="{{ route('products.portfolio') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">
                                    Wis filters
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            @endif

            @if($products->isEmpty())
                <div class="bg-gray-50 rounded-lg p-12 text-center border border-gray-200">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    @if(request()->hasAny(['type','complexity','status','search']))
                        <p class="text-gray-600 text-lg">Geen producten gevonden met deze filters</p>
                        <a href="{{ route('products.portfolio') }}" class="text-orange-500 hover:underline mt-4 inline-block font-semibold">Wis alle filters</a>
                    @else
                        <p class="text-gray-600 text-lg">Je hebt nog geen producten aangemaakt</p>
                        <a href="{{ route('products.create') }}" class="text-orange-500 hover:underline mt-4 inline-block font-semibold">Maak je eerste product</a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Product Header -->
                            <div class="bg-gray-100 h-40 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 flex-1">{{ $product->name }}</h3>
                                    <span class="text-orange-500 font-bold text-lg ml-2">€{{ number_format($product->price_credit, 2) }}</span>
                                </div>

                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>

                                <!-- Tags -->
                                <div class="flex gap-2 flex-wrap mb-4">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                        {{ $product->productType->name }}
                                    </span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded capitalize">
                                        {{ ucfirst($product->complexity->value) }}
                                    </span>
                                    @if($product->approval_status === 'approved')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                            Goedgekeurd
                                        </span>
                                    @elseif($product->approval_status === 'pending')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                            In afwachting
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">
                                            Afgewezen
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-3 rounded text-center text-sm">
                                        Bewerken
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded text-sm" onclick="return confirm('Weet je het zeker?')">
                                            Verwijder
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
