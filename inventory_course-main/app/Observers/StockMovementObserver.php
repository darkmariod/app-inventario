<?php

namespace App\Observers;

use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        DB::transaction(function () use ($stockMovement) {
            $product = $stockMovement->product()->lockForUpdate()->first();

            if (! $product) {
                Log::error("StockMovement sin product: id={$stockMovement->id}");

                return;
            }

            if ($stockMovement->quantity < 0) {
                throw new \InvalidArgumentException('Quantity must be positive.');
            }

            switch ($stockMovement->type) {
                case 'in':
                    $product->stock += $stockMovement->quantity;
                    break;
                case 'out':
                case 'sale':
                    if ($product->stock < $stockMovement->quantity) {
                        throw new \RuntimeException(
                            "Stock insuficiente para producto {$product->sku}. "
                            ."Disponible: {$product->stock}, Solicitado: {$stockMovement->quantity}"
                        );
                    }
                    $product->stock -= $stockMovement->quantity;
                    break;
                case 'return':
                    $product->stock += $stockMovement->quantity;
                    break;
                case 'adjust':
                    $product->stock = $stockMovement->quantity;
                    break;
            }
            $product->save();
        });
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
