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
            'name' => 'required|string|max:255',
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
            'name' => 'required|string|max:255',
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
        //
    }
}
