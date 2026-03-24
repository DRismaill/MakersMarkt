<x-layouts::app>
    <div class="container mx-auto mt-10 max-w-2xl">
        <div class="mb-5">
            <h1 class="text-3xl font-bold">Edit Product</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <!-- Product Type -->
            <div class="mb-4">
                <label for="product_type_id" class="block text-gray-700 text-sm font-bold mb-2">Product Type</label>
                <select id="product_type_id" name="product_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="">Select a product type</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}" {{ $product->product_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_type_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ $product->name }}" required>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" rows="4" required>{{ $product->description }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Material -->
            <div class="mb-4">
                <label for="material" class="block text-gray-700 text-sm font-bold mb-2">Material</label>
                <input type="text" id="material" name="material" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ $product->material }}" required>
                @error('material')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Production Time Days -->
            <div class="mb-4">
                <label for="production_time_days" class="block text-gray-700 text-sm font-bold mb-2">Production Time (Days)</label>
                <input type="number" id="production_time_days" name="production_time_days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ $product->production_time_days }}" min="1" required>
                @error('production_time_days')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Complexity -->
            <div class="mb-4">
                <label for="complexity" class="block text-gray-700 text-sm font-bold mb-2">Complexity Level</label>
                <select id="complexity" name="complexity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="">Select complexity level</option>
                    @foreach ($complexityLevels as $level)
                        <option value="{{ $level->value }}" {{ $product->complexity->value == $level->value ? 'selected' : '' }}>
                            {{ ucfirst($level->value) }}
                        </option>
                    @endforeach
                </select>
                @error('complexity')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Durability -->
            <div class="mb-4">
                <label for="durability" class="block text-gray-700 text-sm font-bold mb-2">Durability Level</label>
                <select id="durability" name="durability" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="">Select durability level</option>
                    @foreach ($durabilityLevels as $level)
                        <option value="{{ $level->value }}" {{ $product->durability->value == $level->value ? 'selected' : '' }}>
                            {{ ucfirst($level->value) }}
                        </option>
                    @endforeach
                </select>
                @error('durability')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Unique Feature -->
            <div class="mb-4">
                <label for="unique_feature" class="block text-gray-700 text-sm font-bold mb-2">Unique Feature</label>
                <textarea id="unique_feature" name="unique_feature" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" rows="3" required>{{ $product->unique_feature }}</textarea>
                @error('unique_feature')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price in Credits -->
            <div class="mb-4">
                <label for="price_credit" class="block text-gray-700 text-sm font-bold mb-2">Price (Credits)</label>
                <input type="number" id="price_credit" name="price_credit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ $product->price_credit }}" min="0" step="0.01" required>
                @error('price_credit')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Has External Link -->
            <div class="mb-6">
                <label for="has_external_link" class="flex items-center">
                    <input type="checkbox" id="has_external_link" name="has_external_link" class="mr-2" {{ $product->has_external_link ? 'checked' : '' }}>
                    <span class="text-gray-700 text-sm">Product has external link</span>
                </label>
                @error('has_external_link')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                    Update Product
                </button>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts::app>
