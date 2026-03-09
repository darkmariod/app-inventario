<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentItem;
use App\Models\Sale;
use App\Models\SaleItem;

class DocumentService
{
    public function createFromSale(Sale $sale): Document
    {
        $document = $this->createDocumentHeader($sale);
        $this->createDocumentItems($sale, $document);

        return $document;
    }

    protected function createDocumentHeader(Sale $sale): Document
    {
        $establecimiento = $sale->customer->codigo_establecimiento ?? '001';
        $puntoEmision = $sale->customer->codigo_punto_emision ?? '001';
        $secuencial = $this->getNextSecuencial($establecimiento, $puntoEmision);

        $numeroDocumento = sprintf(
            '%s-%s-%s',
            $establecimiento,
            $puntoEmision,
            str_pad($secuencial, 9, '0', STR_PAD_LEFT)
        );

        $document = Document::create([
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'user_id' => $sale->user_id,
            'tipo_documento' => Document::TIPO_FACTURA,
            'establecimiento' => $establecimiento,
            'punto_emision' => $puntoEmision,
            'secuencial' => $secuencial,
            'numero_documento' => $numeroDocumento,
            'estado_sri' => Document::ESTADO_BORRADOR,
            'fecha_emision' => $sale->fecha_venta,

            // Totales
            'subtotal_0' => $sale->subtotal_0,
            'subtotal_12' => $sale->subtotal_12,
            'descuento' => $sale->descuento,
            'iva_12' => $sale->iva_12,
            'total' => $sale->total,
        ]);

        return $document;
    }

    protected function createDocumentItems(Sale $sale, Document $document): void
    {
        $orden = 1;

        foreach ($sale->items as $saleItem) {
            $this->createDocumentItemFromSaleItem($saleItem, $document, $orden);
            $orden++;
        }
    }

    protected function createDocumentItemFromSaleItem(SaleItem $saleItem, Document $document, int $orden): DocumentItem
    {
        $product = $saleItem->product;

        return DocumentItem::create([
            'document_id' => $document->id,
            'codigo_principal' => $product?->sku,
            'codigo_auxiliar' => null,
            'descripcion' => $saleItem->producto_nombre ?? $product?->name ?? 'Producto',
            'cantidad' => $saleItem->cantidad,
            'unidad_medida' => 'UNIDAD',
            'precio_unitario' => $saleItem->precio_unitario,
            'descuento_porcentaje' => $saleItem->descuento_porcentaje,
            'descuento_valor' => $saleItem->descuento_valor,
            'iva_porcentaje' => $saleItem->iva_porcentaje,
            'iva_valor' => $saleItem->iva_valor,
            'precio_total_sin_impuestos' => $saleItem->subtotal,
            'precio_total' => $saleItem->total,
            'orden' => $orden,
        ]);
    }

    protected function getNextSecuencial(string $establecimiento, string $puntoEmision): int
    {
        $lastDocument = Document::where('establecimiento', $establecimiento)
            ->where('punto_emision', $puntoEmision)
            ->orderBy('secuencial', 'desc')
            ->first();

        return (int) ($lastDocument?->secuencial ?? 0) + 1;
    }

    public function generateClaveAcceso(Document $document): string
    {
        $fecha = $document->fecha_emision->format('dmY');
        $tipoDoc = $document->tipo_documento;
        $ruc = $document->customer->identificacion;
        $establecimiento = $document->establecimiento;
        $puntoEmision = $document->punto_emision;
        $secuencial = str_pad($document->secuencial, 9, '0', STR_PAD_LEFT);
        $codigoNumerico = str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT);
        $tipoEmision = '1';

        $claveSinDigito = sprintf(
            '%s%s%s%s%s%s%s%s',
            $fecha,
            $tipoDoc,
            $ruc,
            $establecimiento,
            $puntoEmision,
            $secuencial,
            $codigoNumerico,
            $tipoEmision
        );

        $digitoVerificador = $this->calculateModulo11($claveSinDigito);

        return $claveSinDigito.$digitoVerificador;
    }

    protected function calculateModulo11(string $clave): int
    {
        $multipliers = [2, 3, 4, 5, 6, 7, 8, 9];
        $claveReversed = strrev($clave);
        $sum = 0;

        for ($i = 0; $i < strlen($claveReversed); $i++) {
            $sum += (int) $claveReversed[$i] * $multipliers[$i % 8];
        }

        $remainder = $sum % 11;
        $result = 11 - $remainder;

        return match ($result) {
            11 => 0,
            10 => 1,
            default => $result,
        };
    }

    public function markAsReady(Document $document): Document
    {
        $document->update([
            'estado_sri' => Document::ESTADO_PENDIENTE,
            'clave_acceso' => $this->generateClaveAcceso($document),
        ]);

        return $document->fresh();
    }
}
