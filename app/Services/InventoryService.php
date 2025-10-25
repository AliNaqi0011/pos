<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\ReorderRule;
use App\Models\ProductBatch;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InventoryService
{
    public function recordStockMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            
            // Calculate new balance
            $currentBalance = $product->quantity;
            $newBalance = match($data['type']) {
                'in' => $currentBalance + $data['quantity'],
                'out' => $currentBalance - $data['quantity'],
                'adjustment' => $data['quantity'],
                'transfer' => $currentBalance - $data['quantity'],
            };
            
            // Update product quantity
            $product->update(['quantity' => $newBalance]);
            
            // Record movement
            $movement = StockMovement::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'balance_after' => $newBalance,
                'reference_type' => $data['reference_type'],
                'reference_id' => $data['reference_id'],
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'tenant_id' => auth()->user()->tenant_id,
            ]);
            
            // Check reorder rules
            $this->checkReorderRules($product);
            
            return $movement;
        });
    }

    public function processAutoReorders(): array
    {
        $reorderRules = ReorderRule::with(['product', 'supplier'])
            ->where('auto_reorder', true)
            ->where('is_active', true)
            ->whereHas('product', function($query) {
                $query->whereRaw('quantity <= min_quantity');
            })
            ->get();
            
        $ordersCreated = [];
        
        foreach ($reorderRules as $rule) {
            $purchaseOrder = $this->createPurchaseOrder($rule);
            $ordersCreated[] = $purchaseOrder;
        }
        
        return $ordersCreated;
    }

    public function getInventoryValuation(string $method = 'fifo'): array
    {
        $products = Product::with('batches')->get();
        $totalValue = 0;
        $valuationDetails = [];
        
        foreach ($products as $product) {
            $value = match($method) {
                'fifo' => $this->calculateFifoValue($product),
                'lifo' => $this->calculateLifoValue($product),
                'average' => $this->calculateAverageValue($product),
                default => $this->calculateFifoValue($product),
            };
            
            $valuationDetails[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $product->quantity,
                'unit_value' => $value['unit_cost'],
                'total_value' => $value['total_value'],
            ];
            
            $totalValue += $value['total_value'];
        }
        
        return [
            'method' => $method,
            'total_value' => $totalValue,
            'details' => $valuationDetails,
            'generated_at' => now(),
        ];
    }

    public function getExpiryReport(int $daysAhead = 30): array
    {
        $expiringBatches = ProductBatch::with('product')
            ->where('expiry_date', '<=', now()->addDays($daysAhead))
            ->where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();
            
        return [
            'expiring_soon' => $expiringBatches->map(function($batch) {
                return [
                    'product_name' => $batch->product->name,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $batch->quantity,
                    'expiry_date' => $batch->expiry_date,
                    'days_to_expiry' => now()->diffInDays($batch->expiry_date),
                    'value_at_risk' => $batch->quantity * $batch->cost_price,
                ];
            }),
            'total_value_at_risk' => $expiringBatches->sum(fn($b) => $b->quantity * $b->cost_price),
        ];
    }

    public function performStockAudit(int $warehouseId): array
    {
        $products = Product::where('warehouse_id', $warehouseId)->get();
        $discrepancies = [];
        
        foreach ($products as $product) {
            $systemQuantity = $product->quantity;
            $physicalQuantity = $this->getPhysicalCount($product->id); // Would be input from audit
            
            if ($systemQuantity !== $physicalQuantity) {
                $discrepancies[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'system_quantity' => $systemQuantity,
                    'physical_quantity' => $physicalQuantity,
                    'variance' => $physicalQuantity - $systemQuantity,
                    'value_impact' => ($physicalQuantity - $systemQuantity) * $product->cost_price,
                ];
            }
        }
        
        return [
            'warehouse_id' => $warehouseId,
            'audit_date' => now(),
            'total_products_audited' => $products->count(),
            'discrepancies_found' => count($discrepancies),
            'discrepancies' => $discrepancies,
            'total_value_impact' => array_sum(array_column($discrepancies, 'value_impact')),
        ];
    }

    public function transferStock(int $fromWarehouse, int $toWarehouse, array $items): array
    {
        return DB::transaction(function () use ($fromWarehouse, $toWarehouse, $items) {
            $transferId = 'TRF-' . time();
            $transferredItems = [];
            
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                
                // Check availability in source warehouse
                $availableQty = $this->getWarehouseStock($product->id, $fromWarehouse);
                if ($availableQty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name} in source warehouse");
                }
                
                // Record outbound movement
                $this->recordStockMovement([
                    'product_id' => $product->id,
                    'warehouse_id' => $fromWarehouse,
                    'type' => 'transfer',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transferId,
                    'notes' => "Transfer to warehouse {$toWarehouse}",
                ]);
                
                // Record inbound movement
                $this->recordStockMovement([
                    'product_id' => $product->id,
                    'warehouse_id' => $toWarehouse,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transferId,
                    'notes' => "Transfer from warehouse {$fromWarehouse}",
                ]);
                
                $transferredItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                ];
            }
            
            return [
                'transfer_id' => $transferId,
                'from_warehouse' => $fromWarehouse,
                'to_warehouse' => $toWarehouse,
                'items' => $transferredItems,
                'transfer_date' => now(),
            ];
        });
    }

    public function getInventoryTurnover(Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::with(['saleItems' => function($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate]);
            });
        }])->get();
        
        $turnoverData = [];
        
        foreach ($products as $product) {
            $soldQuantity = $product->saleItems->sum('quantity');
            $avgInventory = ($product->quantity + $soldQuantity) / 2; // Simplified calculation
            
            $turnoverRatio = $avgInventory > 0 ? $soldQuantity / $avgInventory : 0;
            $daysSalesInventory = $turnoverRatio > 0 ? 365 / $turnoverRatio : 0;
            
            $turnoverData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sold_quantity' => $soldQuantity,
                'current_stock' => $product->quantity,
                'turnover_ratio' => round($turnoverRatio, 2),
                'days_sales_inventory' => round($daysSalesInventory, 0),
                'classification' => $this->classifyTurnover($turnoverRatio),
            ];
        }
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'products' => $turnoverData,
            'summary' => [
                'fast_moving' => count(array_filter($turnoverData, fn($p) => $p['classification'] === 'fast')),
                'slow_moving' => count(array_filter($turnoverData, fn($p) => $p['classification'] === 'slow')),
                'dead_stock' => count(array_filter($turnoverData, fn($p) => $p['classification'] === 'dead')),
            ],
        ];
    }

    private function checkReorderRules(Product $product): void
    {
        $reorderRules = ReorderRule::where('product_id', $product->id)
            ->where('is_active', true)
            ->get();
            
        foreach ($reorderRules as $rule) {
            if ($product->quantity <= $rule->min_quantity) {
                if ($rule->auto_reorder) {
                    $this->createPurchaseOrder($rule);
                } else {
                    $this->sendReorderAlert($rule);
                }
            }
        }
    }

    private function createPurchaseOrder(ReorderRule $rule): array
    {
        // This would integrate with your purchase order system
        return [
            'supplier_id' => $rule->supplier_id,
            'product_id' => $rule->product_id,
            'quantity' => $rule->reorder_quantity,
            'status' => 'pending',
            'created_at' => now(),
        ];
    }

    private function sendReorderAlert(ReorderRule $rule): void
    {
        // Send notification to relevant users
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();
        
        foreach ($users as $user) {
            // Send email or notification
        }
    }

    private function calculateFifoValue(Product $product): array
    {
        $batches = $product->batches()->orderBy('created_at')->get();
        $remainingQty = $product->quantity;
        $totalValue = 0;
        $weightedCost = 0;
        
        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;
            
            $qtyFromBatch = min($remainingQty, $batch->quantity);
            $totalValue += $qtyFromBatch * $batch->cost_price;
            $remainingQty -= $qtyFromBatch;
        }
        
        $unitCost = $product->quantity > 0 ? $totalValue / $product->quantity : 0;
        
        return [
            'unit_cost' => $unitCost,
            'total_value' => $totalValue,
        ];
    }

    private function calculateLifoValue(Product $product): array
    {
        $batches = $product->batches()->orderByDesc('created_at')->get();
        $remainingQty = $product->quantity;
        $totalValue = 0;
        
        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;
            
            $qtyFromBatch = min($remainingQty, $batch->quantity);
            $totalValue += $qtyFromBatch * $batch->cost_price;
            $remainingQty -= $qtyFromBatch;
        }
        
        $unitCost = $product->quantity > 0 ? $totalValue / $product->quantity : 0;
        
        return [
            'unit_cost' => $unitCost,
            'total_value' => $totalValue,
        ];
    }

    private function calculateAverageValue(Product $product): array
    {
        $avgCost = $product->batches()->avg('cost_price') ?? $product->cost_price;
        $totalValue = $product->quantity * $avgCost;
        
        return [
            'unit_cost' => $avgCost,
            'total_value' => $totalValue,
        ];
    }

    private function getPhysicalCount(int $productId): int
    {
        // This would be input from physical audit
        // For now, return system quantity (in real implementation, this would be user input)
        return Product::find($productId)->quantity;
    }

    private function getWarehouseStock(int $productId, int $warehouseId): int
    {
        return StockMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->latest()
            ->value('balance_after') ?? 0;
    }

    private function classifyTurnover(float $turnoverRatio): string
    {
        if ($turnoverRatio >= 6) return 'fast';
        if ($turnoverRatio >= 2) return 'medium';
        if ($turnoverRatio > 0) return 'slow';
        return 'dead';
    }
}