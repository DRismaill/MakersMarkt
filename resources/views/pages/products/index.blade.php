<x-layouts::app>
    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-2xl font-bold">Products List</h1>
            {{-- Toon Create button ALLEEN voor makers --}}
            @if(auth()->check() && auth()->user()->role->value === 'maker')
                <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Product
                </a>
            @endif
        </div>
        <table class="min-w-full bg-white rounded-lg">
            <thead>
                <tr class="w-full bg-gray-200 text-gray-600 text-left">
                    <th class="py-3 px-4 font-semibold text-sm">ID</th>
                    <th class="py-3 px-4 font-semibold text-sm">Name</th>
                    <th class="py-3 px-4 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4">{{ $product->id }}</td>
                        <td class="py-3 px-4">{{ $product->name }}</td>
                        <td class="py-3 px-4">
                            {{-- Toon Edit button ALLEEN als jij de maker bent van dit product --}}
                            @if(auth()->check() && auth()->user()->id === $product->maker_id && auth()->user()->role->value === 'maker')
                                <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-sm">
                                    Edit
                                </a>
                            @else
                                {{-- Toon Buy button voor anderen --}}
                                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Buy
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts::app>