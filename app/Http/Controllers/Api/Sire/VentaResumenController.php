<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use App\Models\Sire\SireResumenVentas;
use App\Models\Sire\SireVentas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaResumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta
        $query = SireResumenVentas::query();

        // Filtrar por año si se proporciona
        if ($request->has('anio')) {
            $query->where('anio', $request->anio);
        }

        // Filtrar por RUC si se proporciona
        if ($request->has('num_ruc')) {
            $query->where('num_ruc', $request->num_ruc);
        }

        // Obtener todas las ventas filtradas
        $ventas = $query->get();

        // Agrupar las ventas por año y mes usando map
        $ventasAgrupadas = $ventas->groupBy('anio')->map(function ($ventasPorAnio, $anio) {
            return [
                'anio' => $anio,
                'lisMeses' => $ventasPorAnio->groupBy('mes')->map(function ($ventasPorMes, $mes) {
                    return [
                        'mes' => $mes,
                        'total' => $ventasPorMes->sum('total_ventas'),
                    ];
                })->values(), // Convertir a array indexado
            ];
        })->values(); // Convertir a array indexado

        // Devolver los datos como una respuesta JSON
        return response()->json([
            'message' => 'Datos agrupados por año y mes.',
            'data' => $ventasAgrupadas,
        ], 200);
    }


    public function calcularResumenVentas()
    {
        // Obtener las ventas agrupadas por RUC, año y mes
        $resumenVentas = SireVentas::select(
            'num_ruc',
            DB::raw('YEAR(fec_emision) as anio'),
            DB::raw('MONTH(fec_emision) as mes'),
            DB::raw('SUM(mto_total_cp) as total_ventas')
        )
            ->groupBy('num_ruc', 'anio', 'mes')
            ->get();

        // Guardar los resultados en la tabla resumen_ventas
        foreach ($resumenVentas as $resumen) {
            SireResumenVentas::updateOrCreate(
                [
                    'num_ruc' => $resumen->num_ruc,
                    'anio' => $resumen->anio,
                    'mes' => $resumen->mes,
                ],
                [
                    'total_ventas' => $resumen->total_ventas,
                ]
            );
        }

        return response()->json(['message' => 'Resumen de ventas calculado y guardado correctamente.']);
    }
}
