<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Sync products and master data.
     * Clients send 'last_sync' timestamp.
     * Server returns data modified after that timestamp.
     */
    public function sync(Request $request)
    {
        $lastSync = $request->input('last_sync');
        
        $queryCategories = Category::query();
        $queryProducts = Product::query();
        $queryGroups = ProductGroup::query();

        \Illuminate\Support\Facades\Log::info('Sync Request Received', ['last_sync' => $lastSync]);

        if ($lastSync) {
            $queryCategories->where('updated_at', '>', $lastSync);
            $queryProducts->where('updated_at', '>', $lastSync);
            $queryGroups->where('updated_at', '>', $lastSync);
            
            // Note: Handling deletions would require SoftDeletes or a separate table log.
            // For now, we assume this syncs updates/creates.
        }

        $categories = $queryCategories->get();
        // Load relationships if necessary, but strictly we just need the product data for local DB.
        // We might want to include 'category' name in product if not joining locally, but relational DB in Flutter suggests syncing entities.
        $products = $queryProducts->get(); 
        $groups = $queryGroups->get();

        return response()->json([
            'categories' => $categories,
            'products' => $products,
            'groups' => $groups,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }


    /**
     * Adjust product stock manually.
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:1', // Assuming quantity is always positive, logic handles sign
            'notes' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $product) {
            $qty = $request->quantity;
            $type = $request->type;
            $currentStock = $product->stock;
            
            if ($type === 'add') {
                $product->increment('stock', $qty);
            } elseif ($type === 'subtract') {
                $product->decrement('stock', $qty);
            } elseif ($type === 'set') {
                $product->update(['stock' => $qty]);
            }

            // Record Movement
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'type' => $type === 'subtract' ? 'out' : ($type === 'add' ? 'in' : 'adjustment'),
                'quantity' => $qty, // Movement quantity 
                'reference_type' => 'Manual Adjustment',
                'reference_id' => null, // Could be user ID if needed
                'notes' => $request->notes ?? 'Manual Adjustment',
                'created_at' => $request->created_at ? \Carbon\Carbon::parse($request->created_at) : now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully',
            'product' => $product->fresh(),
        ]);
    }
}
