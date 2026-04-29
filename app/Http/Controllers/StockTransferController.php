<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['product', 'fromWarehouse.branch', 'toWarehouse.branch', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::active()->with('branch')->get();
        $products = Product::orderBy('name')->get();
        $products->each(function ($p) { $p->total_stock = $p->getTotalStock(); });
        return view('stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
            $toWarehouse = Warehouse::findOrFail($request->to_warehouse_id);
            $quantity = $request->quantity;

            // Check stock in source warehouse
            $sourceStock = $product->getStockInWarehouse($fromWarehouse->id);
            if ($sourceStock < $quantity) {
                throw new \Exception("Stok di gudang \"{$fromWarehouse->name}\" tidak mencukupi. Tersedia: {$sourceStock}, Diminta: {$quantity}");
            }

            // Create transfer record
            $transfer = StockTransfer::create([
                'transfer_code' => StockTransfer::generateTransferCode(),
                'product_id' => $product->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'quantity' => $quantity,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Deduct from source warehouse
            DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->where('warehouse_id', $fromWarehouse->id)
                ->decrement('stock', $quantity);

            // Add to destination warehouse (upsert)
            DB::table('product_warehouse')->updateOrInsert(
                ['product_id' => $product->id, 'warehouse_id' => $toWarehouse->id],
                ['stock' => DB::raw('COALESCE(stock, 0) + ' . $quantity), 'updated_at' => now()]
            );

            // Stock movement: OUT from source
            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $fromWarehouse->id,
                'type' => 'out',
                'quantity' => $quantity,
                'reference_type' => 'App\Models\StockTransfer',
                'reference_id' => $transfer->id,
                'notes' => "Transfer keluar ke {$toWarehouse->name} - {$transfer->transfer_code}",
            ]);

            // Stock movement: IN to destination
            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $toWarehouse->id,
                'type' => 'in',
                'quantity' => $quantity,
                'reference_type' => 'App\Models\StockTransfer',
                'reference_id' => $transfer->id,
                'notes' => "Transfer masuk dari {$fromWarehouse->name} - {$transfer->transfer_code}",
            ]);

            DB::commit();
            return redirect()->route('stock-transfers.index')->with('success', "Transfer stok berhasil: {$quantity} {$product->name} dari {$fromWarehouse->name} ke {$toWarehouse->name}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product', 'fromWarehouse.branch', 'toWarehouse.branch', 'user']);
        return view('stock_transfers.show', compact('stockTransfer'));
    }

    /**
     * Get stock for a product in a specific warehouse (AJAX).
     */
    public function getStock(Request $request)
    {
        $product = Product::find($request->product_id);
        $warehouseId = $request->warehouse_id;
        if (!$product || !$warehouseId) {
            return response()->json(['stock' => 0]);
        }
        return response()->json(['stock' => $product->getStockInWarehouse($warehouseId)]);
    }
}
