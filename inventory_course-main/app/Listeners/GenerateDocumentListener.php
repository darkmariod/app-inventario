<?php

namespace App\Listeners;

use App\Events\SaleConfirmed;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Log;

class GenerateDocumentListener
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function handle(SaleConfirmed $event): void
    {
        $sale = $event->sale;

        try {
            $document = $this->documentService->createFromSale($sale);

            Log::info('Documento creado desde venta', [
                'sale_id' => $sale->id,
                'sale_numero' => $sale->numero_venta,
                'document_id' => $document->id,
                'document_numero' => $document->numero_documento,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al generar documento desde venta', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
