<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Sire\SireVentas;
use App\Models\Sire\SireVentasDocModificados;

class VentasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = SireVentas::with('documentoMods')->get();
        return response()->json($ventas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Convertir el formato de fecEmision en cada registro


        // Validar que el campo "registros" esté presente y sea un array
        $validator = Validator::make($request->all(), [
            'registros' => 'required|array',
            'registros.*.id' => 'required|string',
            'registros.*.numRuc' => 'required|string|max:11',
            'registros.*.nomRazonSocial' => 'required|string',
            'registros.*.perPeriodoTributario' => 'required|string|max:6',
            'registros.*.codCar' => 'required|string',
            'registros.*.codTipoCDP' => 'required|string|max:2',
            'registros.*.numSerieCDP' => 'required|string',
            'registros.*.numCDP' => 'required|string',
            'registros.*.codTipoCarga' => 'required|string|max:1',
            'registros.*.codSituacion' => 'required|string|max:1',
            'registros.*.fecEmision' => 'required',
            'registros.*.codTipoDocIdentidad' => 'required|string',
            'registros.*.numDocIdentidad' => 'required|string',
            'registros.*.nomRazonSocialCliente' => 'required|string',
            'registros.*.mtoValFactExpo' => 'required|numeric',
            'registros.*.mtoBIGravada' => 'required|numeric',
            'registros.*.mtoDsctoBI' => 'required|numeric',
            'registros.*.mtoIGV' => 'required|numeric',
            'registros.*.mtoDsctoIGV' => 'required|numeric',
            'registros.*.mtoExonerado' => 'required|numeric',
            'registros.*.mtoInafecto' => 'required|numeric',
            'registros.*.mtoISC' => 'required|numeric',
            'registros.*.mtoBIIvap' => 'required|numeric',
            'registros.*.mtoIvap' => 'required|numeric',
            'registros.*.mtoIcbp' => 'required|numeric',
            'registros.*.mtoOtrosTrib' => 'required|numeric',
            'registros.*.mtoTotalCP' => 'required|numeric',
            'registros.*.codMoneda' => 'required|string|max:3',
            'registros.*.mtoTipoCambio' => 'required|numeric',
            'registros.*.codEstadoComprobante' => 'required|string|max:1',
            'registros.*.desEstadoComprobante' => 'required|string',
            'registros.*.indOperGratuita' => 'required|string|max:1',
            'registros.*.indTipoOperacion' => 'nullable|string|max:4',
            'registros.*.mtoValorOpGratuitas' => 'required|numeric',
            'registros.*.mtoValorFob' => 'required|numeric',
            'registros.*.mtoPorcParticipacion' => 'required|numeric',
            'registros.*.mtoValorFobDolar' => 'required|numeric',
            'registros.*.codTipoMotivoNota' => 'nullable|string',
            'registros.*.documentoMod' => 'nullable|array',
            'registros.*.documentoMod.*.fecEmisionMod' => 'required',
            'registros.*.documentoMod.*.codTipoCDPMod' => 'required|string|max:2',
            'registros.*.documentoMod.*.numSerieCDPMod' => 'required|string',
            'registros.*.documentoMod.*.numCDPMod' => 'required|string',
            'registros.*.numInconsistencias' => 'nullable|integer',
            'registros.*.semaforo' => 'nullable|string',
            'registros.*.liscodInconsistencia' => 'nullable|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación Ichpas',
                'errors' => $validator->errors(),
            ], 422);
        }


        $data = $request->all();
        // Procesar cada registro
        foreach ($data['registros'] as $registro) {
            // Crear o actualizar la venta
            if (isset($registro['fecEmision'])) {
                $registro['fecEmision'] = \Carbon\Carbon::createFromFormat('d/m/Y', $registro['fecEmision'])->format('Y-m-d');
            }
            $venta = SireVentas::updateOrCreate(
                [
                    'id_externo' => $registro['id'],

                ],
                [
                    'cod_car' => $registro['codCar'],
                    'num_ruc' => $registro['numRuc'],
                    'nom_razon_social' => $registro['nomRazonSocial'],
                    'per_periodo_tributario' => $registro['perPeriodoTributario'],
                    'cod_tipo_cdp' => $registro['codTipoCDP'],
                    'num_serie_cdp' => $registro['numSerieCDP'],
                    'num_cdp' => $registro['numCDP'],
                    'cod_tipo_carga' => $registro['codTipoCarga'],
                    'cod_situacion' => $registro['codSituacion'],
                    'fec_emision' => $registro['fecEmision'],
                    'cod_tipo_doc_identidad' => $registro['codTipoDocIdentidad'],
                    'num_doc_identidad' => $registro['numDocIdentidad'],
                    'nom_razon_social_cliente' => $registro['nomRazonSocialCliente'],
                    'mto_val_fact_expo' => $registro['mtoValFactExpo'],
                    'mto_bi_gravada' => $registro['mtoBIGravada'],
                    'mto_dscto_bi' => $registro['mtoDsctoBI'],
                    'mto_igv' => $registro['mtoIGV'],
                    'mto_dscto_igv' => $registro['mtoDsctoIGV'],
                    'mto_exonerado' => $registro['mtoExonerado'],
                    'mto_inafecto' => $registro['mtoInafecto'],
                    'mto_isc' => $registro['mtoISC'],
                    'mto_bi_ivap' => $registro['mtoBIIvap'],
                    'mto_ivap' => $registro['mtoIvap'],
                    'mto_icbp' => $registro['mtoIcbp'],
                    'mto_otros_trib' => $registro['mtoOtrosTrib'],
                    'mto_total_cp' => $registro['mtoTotalCP'],
                    'cod_moneda' => $registro['codMoneda'],
                    'mto_tipo_cambio' => $registro['mtoTipoCambio'],
                    'cod_estado_comprobante' => $registro['codEstadoComprobante'],
                    'des_estado_comprobante' => $registro['desEstadoComprobante'],
                    'ind_oper_gratuita' => $registro['indOperGratuita'],
                    'ind_tipo_operacion' => $registro['indTipoOperacion'] ?? null,
                    'mto_valor_op_gratuitas' => $registro['mtoValorOpGratuitas'],
                    'mto_valor_fob' => $registro['mtoValorFob'],
                    'mto_porc_participacion' => $registro['mtoPorcParticipacion'],
                    'mto_valor_fob_dolar' => $registro['mtoValorFobDolar'],
                    'cod_tipo_motivo_nota' => $registro['codTipoMotivoNota'] ?? null,
                    'num_Inconsistencias' => $registro['numInconsistencias'] ?? null,
                    'semaforo' => $registro['semaforo'] ?? null,
                    'lis_cod_Inconsistencia' => json_encode($registro['liscodInconsistencia'] ?? []),
                ]
            );

            // Crear o actualizar los documentos modificatorios asociados
            if (isset($registro['documentoMod']) && is_array($registro['documentoMod'])) {
                foreach ($registro['documentoMod'] as $documentoMod) {
                    if (isset($documentoMod['fecEmisionMod'])) {
                        $documentoMod['fecEmisionMod'] = \Carbon\Carbon::createFromFormat('d/m/Y', $documentoMod['fecEmisionMod'])->format('Y-m-d');
                    }

                    SireVentasDocModificados::updateOrCreate(
                        [
                            'venta_id' => $venta->id,
                            'num_cdp_mod' => $documentoMod['numCDPMod'], // Buscar por venta_id y num_cdp_mod
                        ],
                        [
                            'fec_emision_mod' => $documentoMod['fecEmisionMod'],
                            'cod_tipo_cdp_mod' => $documentoMod['codTipoCDPMod'],
                            'num_serie_cdp_mod' => $documentoMod['numSerieCDPMod'],
                        ]
                    );
                }
            }
        }

        /* return response()->json(['message' => 'Ventas y documentos modificatorios procesados correctamente.','data' => $data], 201); */
        return response()->json(['message' => 'Ventas y documentos modificatorios procesados correctamente Ichpas.', 'data' => $data], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SireVentas $venta)
    {
        return response()->json($venta->load('documentoMods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Obtener las ventas por num_ruc.
     *
     * @param string $numRuc
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVentasPorRuc($numRuc)
    {
        // Obtener las ventas con sus documentos modificatorios
        $ventas = SireVentas::with('documentoMods')->where('num_ruc', $numRuc)->orderBy('fec_emision', 'desc')->orderBy('num_cdp', 'desc')->paginate(100);

        // Devolver las ventas en formato JSON
        return response()->json($ventas);
    }
    public function obtenerVentasPorPeriodo(Request $request)
    {
        try {
            // Obtener el periodo desde el query string
            $perPeriodo = $request->query('per_periodo');
            $perRuc = $request->query('per_ruc');

            // Validar que se haya proporcionado el periodo
            if (!$perPeriodo) {
                return response()->json([
                    'message' => 'El parámetro "per_periodo" es requerido.',
                ], 400);
            }
            if (!$perRuc) {
                return response()->json([
                    'message' => 'El parámetro "per_Ruc" es requerido.',
                ], 400);
            }
            // Consultar las ventas filtradas por periodo
            //'des_tipo_cdp','fec_venc_pag',
            $ventas = SireVentas::select(
                'id',
                'num_doc_identidad',
                'nom_razon_social_cliente',
                'cod_tipo_cdp',

                'num_serie_cdp',
                'num_cdp',
                'cod_moneda',
                'des_estado_comprobante',
                'cod_estado_comprobante',
                'fec_emision',

                'mto_total_cp'
            )->where('per_periodo_tributario', $perPeriodo)
                ->where('num_ruc', $perRuc)
                ->orderBy('fec_emision', 'desc')
                ->paginate(20);

            // Validar que existan ventas para el periodo
            if ($ventas->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron ventas para el periodo proporcionado.',
                ], 404);
            }

            // Devolver los datos como JSON
            return response()->json([
                'message' => 'Ventas obtenidas correctamente.',
                'data' => $ventas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function ventasPorDiaMesActual(Request $request)
    {
        // Obtener el RUC del query string
        $ruc = $request->query('ruc');

        // Validar que se haya proporcionado un RUC
        if (!$ruc) {
            return response()->json([
                'message' => 'El parámetro "ruc" es requerido.',
            ], 400);
        }
        // Obtener el mes y año actual
        $fechaActual = Carbon::now();
        $mesActual = $fechaActual->month;
        $anioActual = $fechaActual->year;

        // Consulta para sumar las ventas por día del mes actual
        $ventasPorDia = SireVentas::where('num_ruc', $ruc)
            ->whereMonth('fec_emision', now()->month) // Filtrar por el mes actual
            ->whereYear('fec_emision', now()->year) // Filtrar por el año actual
            ->selectRaw('DAY(fec_emision) as dia, SUM(mto_total_cp) as total_ventas')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        // Formatear la respuesta
        $resultado = $ventasPorDia->map(function ($venta) {
            return [
                'dia' => $venta->dia,
                'total_ventas' => $venta->total_ventas,
            ];
        });

        return response()->json([
            'message' => 'Ventas por día del mes actual.',
            'data' => $resultado,
        ], 200);
    }

    // Nuevo método para obtener el total de ventas del mes actual
    public function ventasTotalesMes(Request $request)
    {
        try {
            // Obtener el RUC del query string
            $ruc = $request->query('ruc');

            // Validar que se haya proporcionado un RUC
            if (!$ruc) {
                return response()->json([
                    'message' => 'El parámetro "ruc" es requerido.',
                ], 400);
            }

            // Calcular el total de ventas del mes actual filtradas por RUC
            $totalVentas = SireVentas::where('ruc', $ruc)
                ->whereMonth('fecha', now()->month) // Filtrar por el mes actual
                ->sum('total_ventas'); // Sumar los valores de total_ventas

            // Formatear la respuesta
            return response()->json([
                'message' => 'Total de ventas del mes actual.',
                'data' => [
                    'ruc' => $ruc,
                    'total_ventas' => $totalVentas,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SireVentas $venta)
    {
        $venta->documentoMods()->delete(); // Eliminar documentos modificatorios asociados
        $venta->delete(); // Eliminar la venta
        return response()->json(null, 204);
    }
    public function faltantes(Request $request)
    {
        // Devuelve los IDs de las ventas que NO tienen ningún registro en ventas_archivos
        // Devuelve las ventas que NO tienen ningún registro en ventas_archivos
        $ventas = SireVentas::whereDoesntHave('archivo')
            ->select('id', 'cod_tipo_cdp', 'num_serie_cdp', 'num_cdp', 'num_doc_identidad_proveedor')
            ->where('cod_tipo_cdp', '=', '01')
            ->get();

        return response()->json([
            'ventas_sin_archivos' => $ventas
        ]);
    }
}
