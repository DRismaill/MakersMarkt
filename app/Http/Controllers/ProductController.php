<?php

namespace App\Http\Controllers;


use App\Enums\CreditReasonType;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Order;
use App\Models\CreditTransaction;
use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'material' => 'nullable|string|max:255',
        ]);

        $search = trim($validated['search'] ?? '');
        $productTypeId = $validated['product_type_id'] ?? null;
        $material = trim($validated['material'] ?? '');

        $query = Product::query()
            ->with('productType')
            ->where('is_active', true)
            ->where('is_deleted', false);

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($productTypeId) {
            $query->where('product_type_id', $productTypeId);
        }

        if ($material !== '') {
            $query->where('material', 'like', "%{$material}%");
        }

        $products = $query->orderBy('name')->get();
        $productTypes = ProductType::orderBy('name')->get();

        return view('pages.products.index', [
            'products' => $products,
            'productTypes' => $productTypes,
            'filters' => [
                'search' => $search,
                'product_type_id' => $productTypeId,
                'material' => $material,
            ],
        ]);
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

    /**
     * Place a credit-based order for a product.
     */
    public function buy(string $id)
    {
        $buyerId = auth()->id();
        $product = Product::with('maker')->findOrFail($id);

        if (! $product->maker) {
            return redirect()->route('products.index')->with('error', 'Dit product heeft geen geldige maker en kan niet besteld worden.');
        }

        if ($product->maker_id === $buyerId) {
            return redirect()->route('products.index')->with('error', 'Je kunt je eigen product niet bestellen.');
        }

        try {
            $order = DB::transaction(function () use ($buyerId, $product) {
                $productForPurchase = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

                if (! $productForPurchase->is_active || $productForPurchase->is_deleted) {
                    throw new \RuntimeException('Dit product is niet meer beschikbaar.');
                }

                $buyer = User::whereKey($buyerId)->lockForUpdate()->firstOrFail();
                $maker = User::whereKey($productForPurchase->maker_id)->lockForUpdate()->firstOrFail();

                $price = (float) $productForPurchase->price_credit;
                $buyerBalance = (float) $buyer->credit_balance;

                if ($buyerBalance < $price) {
                    throw new \RuntimeException('Onvoldoende winkelkrediet om deze bestelling te plaatsen.');
                }

                $order = Order::create([
                    'buyer_id' => $buyer->id,
                    'product_id' => $productForPurchase->id,
                    'maker_id' => $maker->id,
                    'status' => OrderStatus::Paid,
                    'status_note' => 'Betaald met winkelkrediet',
                    'price_credit' => $productForPurchase->price_credit,
                ]);

                $buyer->decrement('credit_balance', $price);
                $maker->increment('credit_balance', $price);

                CreditTransaction::create([
                    'from_user_id' => $buyer->id,
                    'to_user_id' => $maker->id,
                    'amount' => (string) $productForPurchase->price_credit,
                    'reason_type' => CreditReasonType::Purchase,
                    'order_id' => $order->id,
                    'created_by_admin_id' => null,
                ]);

                $productForPurchase->update([
                    'is_active' => false,
                ]);

                return $order;
            });
        } catch (\RuntimeException $exception) {
            return redirect()->route('products.index')->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('products.index')
            ->with('success', "Bestelling geplaatst. Orderreferentie: #{$order->id}");
    }
}
