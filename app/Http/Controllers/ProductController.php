<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('pages.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productTypes = ProductType::all();
        $complexityLevels = ComplexityLevel::cases();
        $durabilityLevels = DurabilityLevel::cases();
        return view('pages.products.create', compact('productTypes', 'complexityLevels', 'durabilityLevels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:30',
            'description' => 'required|string',
            'material' => 'required|string|max:255',
            'production_time_days' => 'required|integer|min:1',
            'complexity' => 'required|string',
            'durability' => 'required|string',
            'unique_feature' => 'required|string',
            'price_credit' => 'required|numeric|min:0',
            'has_external_link' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a product.');
        }

        $validated['maker_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_external_link'] = $request->has('has_external_link');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = Product::with(['productType', 'maker', 'productReviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $productTypes = ProductType::all();
        $complexityLevels = ComplexityLevel::cases();
        $durabilityLevels = DurabilityLevel::cases();
        return view('pages.products.edit', compact('product', 'productTypes', 'complexityLevels', 'durabilityLevels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:30',
            'description' => 'required|string',
            'material' => 'required|string|max:255',
            'production_time_days' => 'required|integer|min:1',
            'complexity' => 'required|string',
            'durability' => 'required|string',
            'unique_feature' => 'required|string',
            'price_credit' => 'required|numeric|min:0',
            'has_external_link' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_external_link'] = $request->has('has_external_link');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle explicit image removal
        if ($request->input('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Check if the user is the owner of the product
        if (auth()->id() !== $product->maker_id) {
            abort(403, 'You can only delete your own products');
        }

        // Delete image from storage if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.portfolio')->with('success', 'Product deleted successfully!');
    }

    /**
     * Display the maker's portfolio
     */
    public function portfolio()
    {
        $sort           = request('sort', 'newest');
        $filterType     = request('type');
        $filterComplexity = request('complexity');
        $filterStatus   = request('status');
        $search         = request('search');

        // Start query for maker's products
        $query = Product::where('maker_id', auth()->id());

        // Apply filters
        if ($filterType) {
            $query->where('product_type_id', $filterType);
        }
        if ($filterComplexity) {
            $query->where('complexity', $filterComplexity);
        }
        if ($filterStatus) {
            $query->where('approval_status', $filterStatus);
        }
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price_credit', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_credit', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products     = $query->get();
        $productTypes = ProductType::all();
        $complexityLevels = ComplexityLevel::cases();

        return view('pages.products.portfolio', [
            'products'          => $products,
            'currentSort'       => $sort,
            'productTypes'      => $productTypes,
            'complexityLevels'  => $complexityLevels,
            'filterType'        => $filterType,
            'filterComplexity'  => $filterComplexity,
            'filterStatus'      => $filterStatus,
            'search'            => $search,
        ]);
    }
}
