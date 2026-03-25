<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use Illuminate\Support\Str;

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
        ]);

        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a product.');
        }

        $validated['maker_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_external_link'] = $request->has('has_external_link');

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_external_link'] = $request->has('has_external_link');

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
        
        $product->delete();
        
        return redirect()->route('products.portfolio')->with('success', 'Product deleted successfully!');
    }

    /**
     * Display the maker's portfolio
     */
    public function portfolio()
    {
        // Get the sort parameter from request, default to 'newest'
        $sort = request('sort', 'newest');
        
        // Start query for maker's products
        $query = Product::where('maker_id', auth()->id());
        
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
        
        $products = $query->get();
        
        return view('pages.products.portfolio', [
            'products' => $products,
            'currentSort' => $sort
        ]);
    }
}
