<?php

namespace App\Listeners;

use App\Events\SaleConfirmed;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class DeductStockListener
{
    public function handle(SaleConfirmed $event): void
    {
        $sale = $event->sale;

        foreach ($sale->items as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'type' => 'sale',
                'quantity' => $item->cantidad,
                'reference' => 'SALE-'.$sale->numero_venta,
                'user_id' => $sale->user_id,
                'sale_id' => $sale->id,
                'notes' => 'Stock deductido por venta confirmada # '.$sale->numero_venta,
            ]);
        }

        Log::info('Stock deducido para venta #'.$sale->numero_venta, [
            'sale_id' => $sale->id,
            'items_count' => $sale->items->count(),
        ]);
    }
}
