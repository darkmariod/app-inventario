<?php

namespace App\Providers;

use App\Events\SaleConfirmed;
use App\Listeners\DeductStockListener;
use App\Listeners\GenerateDocumentListener;
use App\Models\StockMovement;
use App\Observers\StockMovementObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        StockMovement::observe(StockMovementObserver::class);

        Event::listen(
            SaleConfirmed::class,
            [DeductStockListener::class, 'handle']
        );

        Event::listen(
            SaleConfirmed::class,
            [GenerateDocumentListener::class, 'handle']
        );
    }
}
