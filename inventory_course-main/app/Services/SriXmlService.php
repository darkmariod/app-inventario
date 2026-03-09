<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentItem;
use Illuminate\Support\Facades\File;

class SriXmlService
{
    protected string $ruc;

    protected string $razonSocial;

    protected string $nombreComercial;

    protected string $direccionMatriz;

    protected string $ambiente = '1'; // 1=pruebas, 2=producción

    protected string $tipoEmision = '1';

    public function setEmisorData(array $data): self
    {
        $this->ruc = $data['ruc'];
        $this->razonSocial = $data['razon_social'];
        $this->nombreComercial = $data['nombre_comercial'] ?? $data['razon_social'];
        $this->direccionMatriz = $data['direccion'];

        return $this;
    }

    public function setAmbiente(string $ambiente): self
    {
        $this->ambiente = $ambiente;

        return $this;
    }

    public function generateXml(Document $document): string
    {
        $xml = $this->buildXmlHeader();
        $xml .= $this->buildInfoTributaria($document);
        $xml .= $this->buildInfoFactura($document);
        $xml .= $this->buildDetalles($document);
        $xml .= $this->buildInfoAdicional($document);
        $xml .= $this->buildXmlFooter();

        $savedPath = $this->saveXml($document, $xml);

        $document->update([
            'xml_generado' => $savedPath,
        ]);

        return $xml;
    }

    protected function buildXmlHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    }

    protected function buildInfoTributaria(Document $document): string
    {
        $tipoDoc = $this->getTipoDocumentoCode($document->tipo_documento);

        return <<<XML
<infoTributaria>
    <ambiente>{$this->ambiente}</ambiente>
    <tipoEmision>{$this->tipoEmision}</tipoEmision>
    <razonSocial>{$this->escapeXml($this->razonSocial)}</razonSocial>
    <nombreComercial>{$this->escapeXml($this->nombreComercial)}</nombreComercial>
    <ruc>{$this->ruc}</ruc>
    <claveAcceso>{$document->clave_acceso}</claveAcceso>
    <codDoc>{$tipoDoc}</codDoc>
    <estab>{$document->establecimiento}</estab>
    <ptoEmi>{$document->punto_emision}</ptoEmi>
    <secuencial>{$document->secuencial}</secuencial>
    <dirMatriz>{$this->escapeXml($this->direccionMatriz)}</dirMatriz>
</infoTributaria>
XML;
    }

    protected function buildInfoFactura(Document $document): string
    {
        $customer = $document->customer;
        $fechaEmision = $document->fecha_emision->format('d/m/Y');

        $tipoId = $customer->tipo_identificacion;
        $razonSocial = $this->escapeXml($customer->razon_social);
        $identificacion = $customer->identificacion;

        $contribuyenteEspecial = $customer->contribuyente_especial ? '1234' : '';
        $obligadoContabilidad = $customer->obligado_contabilidad ?? 'NO';

        $totalSinImpuestos = number_format($document->subtotal_0 + $document->subtotal_12, 2, '.', '');
        $totalDescuento = number_format($document->descuento, 2, '.', '');
        $importeTotal = number_format($document->total, 2, '.', '');

        $impuestosXml = $this->buildTotalImpuestos($document);

        return <<<XML
<infoFactura>
    <fechaEmision>{$fechaEmision}</fechaEmision>
    <dirEstablecimiento>{$this->escapeXml($this->direccionMatriz)}</dirEstablecimiento>
    <contribuyenteEspecial>{$contribuyenteEspecial}</contribuyenteEspecial>
    <obligadoContabilidad>{$obligadoContabilidad}</obligadoContabilidad>
    <tipoIdentificacionComprador>{$tipoId}</tipoIdentificacionComprador>
    <razonSocialComprador>{$razonSocial}</razonSocialComprador>
    <identificacionComprador>{$identificacion}</identificacionComprador>
    <totalSinImpuestos>{$totalSinImpuestos}</totalSinImpuestos>
    <totalDescuento>{$totalDescuento}</totalDescuento>
    {$impuestosXml}
    <importeTotal>{$importeTotal}</importeTotal>
    <moneda>DOLAR</moneda>
</infoFactura>
XML;
    }

    protected function buildTotalImpuestos(Document $document): string
    {
        $xml = '<totalConImpuestos>'."\n";

        // IVA 12%
        if ($document->subtotal_12 > 0) {
            $base12 = number_format($document->subtotal_12, 2, '.', '');
            $valor12 = number_format($document->iva_12, 2, '.', '');
            $xml .= <<<XML
    <totalImpuesto>
        <codigo>2</codigo>
        <codigoPorcentaje>2</codigoPorcentaje>
        <baseImponible>{$base12}</baseImponible>
        <valor>{$valor12}</valor>
    </totalImpuesto>
XML;
        }

        // IVA 14%
        if (isset($document->subtotal_14) && $document->subtotal_14 > 0) {
            $base14 = number_format($document->subtotal_14, 2, '.', '');
            $valor14 = number_format($document->iva_14 ?? 0, 2, '.', '');
            $xml .= <<<XML

    <totalImpuesto>
        <codigo>2</codigo>
        <codigoPorcentaje>3</codigoPorcentaje>
        <baseImponible>{$base14}</baseImponible>
        <valor>{$valor14}</valor>
    </totalImpuesto>
XML;
        }

        // IVA 0%
        if ($document->subtotal_0 > 0) {
            $base0 = number_format($document->subtotal_0, 2, '.', '');
            $xml .= <<<XML

    <totalImpuesto>
        <codigo>2</codigo>
        <codigoPorcentaje>0</codigoPorcentaje>
        <baseImponible>{$base0}</baseImponible>
        <valor>0.00</valor>
    </totalImpuesto>
XML;
        }

        $xml .= '</totalConImpuestos>';

        return $xml;
    }

    protected function buildDetalles(Document $document): string
    {
        $xml = '<detalles>'."\n";

        foreach ($document->items as $item) {
            $xml .= $this->buildDetalle($item);
        }

        $xml .= '</detalles>';

        return $xml;
    }

    protected function buildDetalle(DocumentItem $item): string
    {
        $codigoPrincipal = $this->escapeXml($item->codigo_principal ?? '');
        $descripcion = $this->escapeXml($item->descripcion);
        $cantidad = number_format($item->cantidad, 2, '.', '');
        $precioUnitario = number_format($item->precio_unitario, 4, '.', '');
        $descuento = number_format($item->descuento_valor, 2, '.', '');
        $precioTotalSinImpuesto = number_format($item->precio_total_sin_impuestos, 2, '.', '');

        $impuestosXml = $this->buildImpuestoDetalle($item);

        return <<<XML

        <detalle>
            <codigoPrincipal>{$codigoPrincipal}</codigoPrincipal>
            <descripcion>{$descripcion}</descripcion>
            <cantidad>{$cantidad}</cantidad>
            <precioUnitario>{$precioUnitario}</precioUnitario>
            <descuento>{$descuento}</descuento>
            <precioTotalSinImpuesto>{$precioTotalSinImpuesto}</precioTotalSinImpuesto>
            <impuestos>
                {$impuestosXml}
            </impuestos>
        </detalle>
XML;
    }

    protected function buildImpuestoDetalle(DocumentItem $item): string
    {
        if ($item->iva_valor <= 0) {
            return '';
        }

        $codigoPorcentaje = $this->getIvaCodigoPorcentaje($item->iva_porcentaje);
        $baseImponible = number_format($item->precio_total_sin_impuestos, 2, '.', '');
        $valor = number_format($item->iva_valor, 2, '.', '');

        return <<<XML
                <impuesto>
                    <codigo>2</codigo>
                    <codigoPorcentaje>{$codigoPorcentaje}</codigoPorcentaje>
                    <tarifa>{$item->iva_porcentaje}</tarifa>
                    <baseImponible>{$baseImponible}</baseImponible>
                    <valor>{$valor}</valor>
                </impuesto>
XML;
    }

    protected function buildInfoAdicional(Document $document): string
    {
        $customer = $document->customer;

        $campos = [];

        if ($customer->email) {
            $campos[] = '<campoAdicional nombre="email">'.$this->escapeXml($customer->email).'</campoAdicional>';
        }

        if ($customer->telefono) {
            $campos[] = '<campoAdicional nombre="telefono">'.$this->escapeXml($customer->telefono).'</campoAdicional>';
        }

        if ($customer->direccion) {
            $campos[] = '<campoAdicional nombre="direccion">'.$this->escapeXml($customer->direccion).'</campoAdicional>';
        }

        if (empty($campos)) {
            return '';
        }

        return '<infoAdicional>'."\n    ".implode("\n    ", $campos)."\n".'</infoAdicional>';
    }

    protected function buildXmlFooter(): string
    {
        return '</factura>';
    }

    protected function getTipoDocumentoCode(string $tipo): string
    {
        return match ($tipo) {
            '01' => '01', // Factura
            '04' => '04', // Nota de Crédito
            '05' => '05', // Nota de Débito
            '06' => '06', // Guía de Remisión
            '07' => '07', // Retención
            default => '01',
        };
    }

    protected function getIvaCodigoPorcentaje(float $porcentaje): string
    {
        return match ($porcentaje) {
            0 => '0',
            12 => '2',
            14 => '3',
            default => '0',
        };
    }

    protected function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }

    protected function saveXml(Document $document, string $xml): string
    {
        $path = storage_path('app/sri/xml');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = sprintf(
            '%s_%s.xml',
            $document->clave_acceso ?? $document->numero_documento,
            now()->format('YmdHis')
        );

        $fullPath = $path.'/'.$filename;

        File::put($fullPath, $xml);

        return 'sri/xml/'.$filename;
    }
}
