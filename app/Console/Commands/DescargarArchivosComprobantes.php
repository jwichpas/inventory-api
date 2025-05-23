<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sire\SireVentas;
use App\Models\Sire\SireVentasArchivo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DescargarArchivosComprobantes extends Command
{
    protected $signature = 'comprobantes:descargar-archivos';
    protected $description = 'Descarga XML, CDR, PDF y guía de comprobantes faltantes';
    public function handle()
    {
        // Procesa ventas (haz lo mismo para compras si lo deseas)
        $ventas = SireVentas::with('archivo')
            ->whereHas('archivo', function ($q) {
                $q->whereNull('xml')->orWhereNull('pdf')->orWhereNull('cdr'); // agrega guía si corresponde
            })
            ->get();

        foreach ($ventas as $venta) {
            $archivo = $venta->archivo;
            $basePath = 'comprobantes/ventas/' . $venta->id . '/';

            // Descargar XML
            if (!$archivo->xml) {
                $xml = $this->descargarArchivoXml($venta);
                if ($xml) {
                    Storage::disk('public')->put($basePath . 'documento.xml', $xml);
                    $archivo->xml = $basePath . 'documento.xml';
                }
            }
            // Descargar PDF
            if (!$archivo->pdf) {
                $pdf = $this->descargarArchivoPdf($venta);
                if ($pdf) {
                    Storage::disk('public')->put($basePath . 'documento.pdf', $pdf);
                    $archivo->pdf = $basePath . 'documento.pdf';
                }
            }
            // Descargar CDR (haz tu propia función si la API lo permite)
            if (!$archivo->cdr) {
                $cdr = $this->descargarArchivoCdr($venta);
                if ($cdr) {
                    Storage::disk('public')->put($basePath . 'documento.cdr', $cdr);
                    $archivo->cdr = $basePath . 'documento.cdr';
                }
            }
            // Descargar Guía si aplica
            // ...

            $archivo->save();
        }

        $this->info('Descarga completada.');
    }
}
