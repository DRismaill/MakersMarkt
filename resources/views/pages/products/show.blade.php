@use('Illuminate\Support\Facades\Storage')
<x-layouts::app>
    <div class="min-h-screen bg-gray-50">

        <!-- Breadcrumb -->
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-3">
                <nav class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('products.index') }}" class="hover:text-orange-500 transition">Productcatalogus</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900 font-medium truncate">{{ $product->name }}</span>
                </nav>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left column: image + specs -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Product Image -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                 class="w-full h-80 object-cover">
                        @else
                            <div class="w-full h-80 bg-gray-100 flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Name, price, badges -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex justify-between items-start gap-4 mb-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                            <span class="text-2xl font-bold text-orange-500 whitespace-nowrap">
                                €{{ number_format($product->price_credit, 2) }}
                            </span>
                        </div>

                        <!-- Badges -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                                {{ $product->productType->name }}
                            </span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full capitalize">
                                {{ $product->complexity->value }}
                            </span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full capitalize">
                                Duurzaamheid: {{ $product->durability->value }}
                            </span>
                            @if($product->approval_status->value === 'approved')
                                <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-semibold rounded-full">
                                    ✓ Goedgekeurd
                                </span>
                            @elseif($product->approval_status->value === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                    ⏳ In afwachting
                                </span>
                            @endif
                            @if($product->needs_moderation)
                                <span class="px-3 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full">
                                    Gemarkeerd voor moderatie
                                </span>
                            @endif
                        </div>

                        <!-- Rating summary -->
                        @if($product->review_count > 0)
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 fill-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.95 2.678c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.065 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="font-semibold text-gray-800">{{ number_format($product->average_rating, 1) }}</span>
                                <span class="text-gray-400 text-sm">({{ $product->review_count }} {{ $product->review_count === 1 ? 'beoordeling' : 'beoordelingen' }})</span>
                            </div>
                        @endif

                        <!-- Description -->
                        <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <!-- Specs card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Productdetails</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Materiaal</dt>
                                <dd class="text-gray-800 font-medium">{{ $product->material }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Productietijd</dt>
                                <dd class="text-gray-800 font-medium">{{ $product->production_time_days }} {{ $product->production_time_days === 1 ? 'dag' : 'dagen' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Complexiteit</dt>
                                <dd class="text-gray-800 font-medium capitalize">{{ $product->complexity->value }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Duurzaamheid</dt>
                                <dd class="text-gray-800 font-medium capitalize">{{ $product->durability->value }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 sm:col-span-2">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Uniek kenmerk</dt>
                                <dd class="text-gray-800 font-medium">{{ $product->unique_feature }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Reviews -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">
                            Beoordelingen
                            @if($product->review_count > 0)
                                <span class="text-sm font-normal text-gray-400 ml-1">({{ $product->review_count }})</span>
                            @endif
                        </h2>

                        @if($product->productReviews->isEmpty())
                            <p class="text-gray-400 text-sm">Nog geen beoordelingen voor dit product.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($product->productReviews as $review)
                                    <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold text-gray-800 text-sm">{{ $review->user->username }}</span>
                                            <span class="text-gray-400 text-xs">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 fill-gray-300' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.95 2.678c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.065 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Right column: buy card + maker card -->
                <div class="space-y-6">

                    <!-- Buy card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-6">
                        <div class="text-2xl font-bold text-orange-500 mb-1">
                            €{{ number_format($product->price_credit, 2) }}
                        </div>
                        <p class="text-gray-400 text-xs mb-4">Prijs in credits</p>

                        @if(auth()->check() && auth()->user()->id === $product->maker_id)
                            <a href="{{ route('products.edit', $product->id) }}"
                               class="block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition mb-3">
                                Bewerken
                            </a>
                        @else
                            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition mb-3">
                                Kopen
                            </button>
                        @endif

                        <a href="{{ route('products.index') }}"
                           class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg text-sm transition">
                            ← Terug naar catalogus
                        </a>

                        <!-- Quick specs -->
                        <div class="mt-5 pt-5 border-t border-gray-100 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Productietijd</span>
                                <span class="font-medium text-gray-700">{{ $product->production_time_days }} dagen</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Materiaal</span>
                                <span class="font-medium text-gray-700">{{ $product->material }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Type</span>
                                <span class="font-medium text-gray-700">{{ $product->productType->name }}</span>
                            </div>
                            @if($product->has_external_link)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Externe link</span>
                                    <span class="font-medium text-green-600">✓ Ja</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Maker card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Over de maker</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg shrink-0">
                                {{ strtoupper(substr($product->maker->username, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $product->maker->username }}</p>
                                <p class="text-xs text-gray-400">Maker</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
