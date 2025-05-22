<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireResumenDetalle;

class ReporteResumenDetalleController extends Controller
{
    public function index(Request $request)
    {
        //http://127.0.0.1:8000/api/v1/reportecumplimiento/detalleresumen?numRuc=10443121507
        // Obtener los parámetros de consulta (query parameters)
        $numRuc = $request->query('numRuc');
        $perTributario = $request->query('perTributario');

        // Construir la consulta base
        $query = SireResumenDetalle::query()
            ->orderBy('perTributario', 'desc');

        // Aplicar filtros si están presentes
        if ($numRuc) {
            $query->where('numRuc', $numRuc);
        }

        if ($perTributario) {
            $query->where('perTributario', $perTributario);
        }

        // Obtener los resultados (paginados o no)
        $detalles = $query->get();

        // Retornar la respuesta en formato JSON
        return response()->json([
            'data' => $detalles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'lisDetallesResumen' => 'required|array',
            'lisDetallesResumen.*.correlativo' => 'required|integer',
            'lisDetallesResumen.*.numRuc' => 'required|string',
            'lisDetallesResumen.*.nomRazonSocial' => 'required|string',
            'lisDetallesResumen.*.perTributario' => 'required|string',
            'lisDetallesResumen.*.nomRegistro' => 'required|string',
            'lisDetallesResumen.*.constancia' => 'required|string',
            'lisDetallesResumen.*.nomArchivoConstanciaComprasPdf' => 'nullable|string',
            'lisDetallesResumen.*.nomArchivoConstanciaVentasPdf' => 'nullable|string',
            'lisDetallesResumen.*.fechGeneracion' => 'nullable|string',
            'lisDetallesResumen.*.fechVencimiento' => 'required|string',
            'lisDetallesResumen.*.codEstadoGeneracion' => 'required|string',
            'lisDetallesResumen.*.desEstadoGeneracion' => 'required|string',
            'lisDetallesResumen.*.perTributarioFormateado' => 'required|string',
        ]);

        // Recorrer cada elemento del array lisDetallesResumen
        foreach ($request->lisDetallesResumen as $detalle) {
            // Convertir fechas al formato correcto
            $fechGeneracion = $detalle['fechGeneracion'] ? \Carbon\Carbon::createFromFormat('d/m/Y', $detalle['fechGeneracion'])->format('Y-m-d') : null;
            $fechVencimiento = \Carbon\Carbon::createFromFormat('d/m/Y', $detalle['fechVencimiento'])->format('Y-m-d');
            // Usar updateOrCreate para guardar o actualizar el registro
            SireResumenDetalle::updateOrCreate(
                [
                    'numRuc' => $detalle['numRuc'],
                    'perTributario' => $detalle['perTributario'],
                    'nomRegistro' => $detalle['nomRegistro'],
                ], // Condición para buscar el registro
                [
                    'nomRazonSocial' => $detalle['nomRazonSocial'],
                    'correlativo' => $detalle['correlativo'],
                    'constancia' => $detalle['constancia'],
                    'nomArchivoConstanciaComprasPdf' => $detalle['nomArchivoConstanciaComprasPdf'],
                    'nomArchivoConstanciaVentasPdf' => $detalle['nomArchivoConstanciaVentasPdf'],
                    'fechGeneracion' => $fechGeneracion,
                    'fechVencimiento' => $fechVencimiento,
                    'codEstadoGeneracion' => $detalle['codEstadoGeneracion'],
                    'desEstadoGeneracion' => $detalle['desEstadoGeneracion'],
                    'perTributarioFormateado' => $detalle['perTributarioFormateado'],
                ]
            );
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'message' => 'Registros guardados o actualizados exitosamente',
        ], 201);
    }
}
