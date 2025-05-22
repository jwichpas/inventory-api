<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireResumen;

class ReporteResumenController extends Controller
{
    public function index(Request $request)
    {
        //curl -X GET http://127.0.0.1:8000/api/v1/reportecumplimiento/resumen?numRuc=10443121507&perTributario=2025
        // Obtener los parámetros de consulta (query parameters)
        $numRuc = $request->query('numRuc');
        $perTributario = $request->query('perTributario');

        // Construir la consulta base
        $query = SireResumen::query();

        // Aplicar filtros si están presentes
        if ($numRuc) {
            $query->where('numRuc', $numRuc);
        }

        if ($perTributario) {
            $query->where('perTributario', $perTributario);
        }

        // Obtener los resultados (paginados o no)
        $resumenes = $query->get();

        // Retornar la respuesta en formato JSON
        return response()->json([
            'data' => $resumenes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'lisResumen' => 'required|array',
            'lisResumen.*.numRuc' => 'required|string',
            'lisResumen.*.nomRazonSocial' => 'required|string',
            'lisResumen.*.perTributario' => 'required|string',
            'lisResumen.*.cntRegistrosPresentadosDP' => 'required|integer',
            'lisResumen.*.cntRegistrosPresentadosFP' => 'required|integer',
            'lisResumen.*.cntRegistrosPresentadosNG' => 'required|integer',
            'lisResumen.*.cntRegistrosPresentados' => 'required|integer',
        ]);

        // Recorrer cada elemento del array lisResumen
        foreach ($request->lisResumen as $resumen) {
            // Usar updateOrCreate para guardar o actualizar el registro
            SireResumen::updateOrCreate(
                [
                    'numRuc' => $resumen['numRuc'],
                    'perTributario' => $resumen['perTributario']
                ], // Condición para buscar el registro
                [
                    'nomRazonSocial' => $resumen['nomRazonSocial'],
                    'cntRegistrosPresentadosDP' => $resumen['cntRegistrosPresentadosDP'],
                    'cntRegistrosPresentadosFP' => $resumen['cntRegistrosPresentadosFP'],
                    'cntRegistrosPresentadosNG' => $resumen['cntRegistrosPresentadosNG'],
                    'cntRegistrosPresentados' => $resumen['cntRegistrosPresentados'],
                ]
            );
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'message' => 'Registros guardados o actualizados exitosamente',
        ], 201);
    }
}
