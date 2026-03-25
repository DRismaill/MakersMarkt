@use('Illuminate\Support\Facades\Storage')
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

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
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
                <input type="text" id="name" name="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror"
                    value="{{ old('name', $product->name) }}"
                    maxlength="30"
                    oninput="updateNameCounter(this)"
                    required>
                <div class="flex justify-between items-center mt-1">
                    <div>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <span id="name-counter" class="text-gray-400 text-xs">0 / 30</span>
                </div>
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

            <!-- Image -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Productafbeelding
                    <span class="text-gray-400 font-normal">(optioneel · jpeg, png, webp · max 2 MB)</span>
                </label>

                @if ($product->image)
                    <div id="current-image" class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Huidige afbeelding:</p>
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                             class="w-40 h-40 object-cover rounded-lg border border-gray-200 shadow-sm">
                        <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer select-none">
                            <input type="checkbox" name="remove_image" value="1" class="rounded" onchange="toggleRemoveImage(this)">
                            Afbeelding verwijderen
                        </label>
                    </div>
                @endif

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    onchange="previewImage(event)"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 @error('image') border border-red-500 rounded-lg @enderror"
                >
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div id="image-preview" class="mt-3 hidden">
                    <p class="text-xs text-gray-500 mb-1">Nieuwe afbeelding:</p>
                    <img id="preview-img" src="" alt="Preview" class="w-40 h-40 object-cover rounded-lg border border-gray-200 shadow-sm">
                </div>
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

    <script>
        function updateNameCounter(input) {
            const max = 30;
            const len = input.value.length;
            const counter = document.getElementById('name-counter');
            counter.textContent = len + ' / ' + max;
            if (len >= max) {
                counter.classList.add('text-red-500');
                counter.classList.remove('text-gray-400');
            } else {
                counter.classList.remove('text-red-500');
                counter.classList.add('text-gray-400');
            }
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const preview = document.getElementById('image-preview');
            const img     = document.getElementById('preview-img');
            img.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }

        function toggleRemoveImage(checkbox) {
            const currentImage = document.getElementById('current-image');
            if (currentImage) {
                currentImage.style.opacity = checkbox.checked ? '0.4' : '1';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('name');
            if (nameInput) updateNameCounter(nameInput);
        });
    </script>
</x-layouts::app>
