<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireCompras;
use App\Models\Sire\SireComprasTipoCambio;
use App\Models\Sire\SireComprasMonto;
use App\Models\Sire\SireComprasDocModificados;
use App\Models\Sire\SireComprasAuditoria;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ComprasController extends Controller
{
    // Obtener todas las compras
    public function index()
    {
        $compras = SireCompras::with(['tipoCambio', 'montos', 'documentosModificados', 'auditoria'])->orderBy('fec_emision', 'desc')->get();
        return response()->json($compras);
    }

    // Crear múltiples compras
    public function store(Request $request)
    {
        // Validar que el campo "registros" sea un array
        $validator = Validator::make($request->all(), [
            'registros' => 'required|array',
            'registros.*.lisDocumentosMod' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación: El campo "registros" debe ser un array.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Array para almacenar los resultados y errores de cada línea
        $results = [];
        $errorLines = []; // Para almacenar las líneas con errores

        // Iterar sobre cada registro en el array "registros"
        foreach ($request->input('registros') as $index => $registro) {
            // Validar cada línea individualmente
            $lineValidator = Validator::make($registro, [
                'id' => 'required|string',
                'numRuc' => 'required',
                'nomRazonSocial' => 'required',
                'codCar' => 'required',
                'codTipoCDP' => 'required',
                'desTipoCDP' => 'required',
                'numSerieCDP' => 'required',
                'numCDP' => 'required',
                'fecEmision' => 'required|date',
                'fecVencPag' => 'nullable|date',
                'numCDPRangoFinal' => 'nullable',
                'codTipoDocIdentidadProveedor' => 'required',
                'numDocIdentidadProveedor' => 'required',
                'nomRazonSocialProveedor' => 'required',
                'codTipoCarga' => 'required',
                'codSituacion' => 'required',
                'codMoneda' => 'required',
                'montos.mtoTotalCp' => 'required|numeric',
                'codEstadoComprobante' => 'nullable',
                'desEstadoComprobante' => 'nullable',
                'indOperGratuita' => 'nullable',
                'codTipoMotivoNota' => 'nullable',
                'desTipoMotivoNota' => 'nullable',
                'indEditable' => 'nullable',
                'perTributario' => 'required',
                'numInconsistencias' => 'nullable',
                'indInfIncompleta' => 'nullable',
                'indModificadoContribuyente' => 'nullable',
                'plazoVisualizacion' => 'nullable',
                'indDetraccion' => 'nullable',
                'indIncluExcluCar' => 'required',
                'porParticipacion' => 'nullable',
                'codBbss' => 'nullable',
                'codIdProyecto' => 'nullable',
                'annCDP' => 'nullable',
                'codDepAduanera' => 'nullable',
                'indFuenteCP' => 'nullable',
                'liscodInconsistencia' => 'nullable',
                'lisNumCasilla' => 'nullable',
                'porTasaRetencion' => 'nullable',
                'desMsjOriginal' => 'nullable',
                'numCarIndIE' => 'nullable',
                'numCorrelativo' => 'nullable',
                'porTasaIGV' => 'nullable',

                'camposLibres' => 'nullable',
                'lisDocumentosMod' => 'nullable|array',
                'lisDocumentosMod.codTipoCDPMod' => 'nullable',
                'lisDocumentosMod.numSerieCDPMod' => 'nullable',
                'lisDocumentosMod.numCDPMod' => 'nullable',
                'lisDocumentosMod.numCDPMod' => 'nullable',

                'archivoCarga' => 'nullable',
                'archivoCarga.numTicket' => 'required_if:archivoCarga,array|string', // Requiere si archivoCarga es un arreglo
                'archivoCarga.numOperacion' => 'required_if:archivoCarga,array|integer',
                'archivoCarga.numRegistro' => 'required_if:archivoCarga,array|integer',
                'archivoCarga.nomArchivo' => 'required_if:archivoCarga,array|string',
            ]);

            // Si hay errores en esta línea, agregarlos al resultado
            if ($lineValidator->fails()) {
                $lineNumber = $index + 1; // Número de línea (basado en índice)
                $errorLines[] = [
                    'linea' => $lineNumber,
                    'errors' => $lineValidator->errors(),
                ];
            } else {
                // Si no hay errores, guardar el registro usando updateOrCreate
                try {

                    $compra = SireCompras::updateOrCreate(
                        [
                            'id_registro' => $registro['id'],
                            'num_ruc' => $registro['numRuc'],
                            'nom_razon_social' => $registro['nomRazonSocial'],
                            'cod_car' => $registro['codCar'],
                            'cod_tipo_cdp' => $registro['codTipoCDP'],
                            'des_tipo_cdp' => $registro['desTipoCDP'],
                            'num_serie_cdp' => $registro['numSerieCDP'],
                            'num_cdp' => $registro['numCDP'],
                            'fec_emision' => $registro['fecEmision'],
                            'fec_venc_pag' => $registro['fecVencPag'] ?? null,
                            'num_cdp_rango_final' => $registro['numCDPRangoFinal'] ?? null,
                            'cod_tipo_doc_identidad_proveedor' => $registro['codTipoDocIdentidadProveedor'],
                            'num_doc_identidad_proveedor' => $registro['numDocIdentidadProveedor'],
                            'nom_razon_social_proveedor' => $registro['nomRazonSocialProveedor'],
                        ],
                        [
                            'cod_tipo_carga' => $registro['codTipoCarga'],
                            'cod_situacion' => $registro['codSituacion'],
                            'cod_moneda' => $registro['codMoneda'],
                            'mto_total_cp' => $registro['montos']['mtoTotalCp'],
                            'cod_estado_comprobante' => $registro['codEstadoComprobante'] ?? null,
                            'des_estado_comprobante' => $registro['desEstadoComprobante'] ?? null,
                            'ind_oper_gratuita' => $registro['indOperGratuita'] ?? null,
                            'cod_tipo_motivo_nota' => $registro['codTipoMotivoNota'] ?? null,
                            'des_tipo_motivo_nota' => $registro['desTipoMotivoNota'] ?? null,
                            'ind_editable' => $registro['indEditable'] ?? null,
                            'per_tributario' => $registro['perTributario'],
                            'num_inconsistencias' => $registro['numInconsistencias'] ?? null,
                            'ind_inf_incompleta' => $registro['indInfIncompleta'] ?? null,
                            'ind_modificado_contribuyente' => $registro['indModificadoContribuyente'] ?? null,
                            'plazo_visualizacion' => $registro['plazoVisualizacion'] ?? null,
                            'ind_detraccion' => $registro['indDetraccion'] ?? null,
                            'ind_inclu_exclu_car' => $registro['indIncluExcluCar'],
                            'por_participacion' => $registro['porParticipacion'] ?? null,
                            'cod_bbss' => $registro['codBbss'] ?? null,
                            'cod_id_proyecto' => $registro['codIdProyecto'] ?? null,
                            'ann_cdp' => $registro['annCDP'] ?? null,
                            'cod_dep_aduanera' => $registro['codDepAduanera'] ?? null,
                            'ind_fuente_cp' => $registro['indFuenteCP'],
                            'lis_cod_inconsistencia' => json_encode($registro['liscodInconsistencia'] ?? []),
                            'lis_num_casilla' => json_encode($registro['lisNumCasilla'] ?? []),
                            'por_tasa_retencion' => $registro['porTasaRetencion'] ?? null,
                            'des_msj_original' => $registro['desMsjOriginal'] ?? null,
                            'num_car_ind_ie' => $registro['numCarIndIE'] ?? null,
                            'num_correlativo' => $registro['numCorrelativo'] ?? null,
                            'por_tasa_igv' => $registro['porTasaIGV'] ?? null,
                            'archivo_carga' => json_encode($registro['archivoCarga'] ?? []),
                            'campos_libres' => json_encode($registro['camposLibres'] ?? []),
                        ]
                    );
                    SireComprasAuditoria::updateorCreate(
                        [
                            'compra_id' => $compra->id,
                        ],
                        [
                            'compra_id' => $compra->id,
                            'cod_usu_regis' => $registro['auditoria']['codUsuRegis'],
                            'fec_regis' => $registro['auditoria']['fecRegis'],
                            'cod_usu_modif' => $registro['auditoria']['codUsuModif'],
                            'fec_modif' => $registro['auditoria']['fecModif'],
                        ]
                    );
                    if (isset($registro['tipoCambio'])) {
                        SireComprasTipoCambio::updateorCreate(
                            [
                                'compra_id' => $compra->id,
                                'ind_carga_tipo_cambio' => $registro['tipoCambio']['indCargaTipoCambio'],
                            ],
                            [
                                'mto_cambio_moneda_extranjera' => $registro['tipoCambio']['mtoCambioMonedaExtranjera'] ?? null,
                                'mto_cambio_moneda_dolares' => $registro['tipoCambio']['mtoCambioMonedaDolares'] ?? null,
                                'mto_tipo_cambio' => $registro['tipoCambio']['mtoTipoCambio'] ?? null,
                            ]
                        );
                        SireComprasMonto::updateorCreate(
                            [
                                'compra_id' => $compra->id
                            ],
                            [
                                'mto_bi_gravada_dg' => $registro['montos']['mtoBIGravadaDG'],
                                'mto_igv_ipm_dg' => $registro['montos']['mtoIgvIpmDG'],
                                'mto_total_cp' => $registro['montos']['mtoTotalCp'],
                            ]
                        );
                    }
                    // Crear los montos
                    /* $compra->montos()->create($registro['montos']); */

                    // Crear los documentos modificados
                    if (isset($registro['lisDocumentosMod']) && is_array($registro['lisDocumentosMod']) && !empty($registro['lisDocumentosMod']) && count(array_filter($registro['lisDocumentosMod'])) > 0) {
                        foreach ($registro['lisDocumentosMod'] as $documentoMod) {

                            if (isset($documentoMod['fecEmisionMod'])) {
                                $fechaEmision = \DateTime::createFromFormat('d/m/Y', $documentoMod['fecEmisionMod']);
                                if (!$fechaEmision) {
                                    return response()->json(['error' => 'Formato de fecha inválido para fecEmisionMod'], 400);
                                }

                                // Convertir la fecha de 'DD/MM/YYYY' a 'YYYY-MM-DD'

                                SireComprasDocModificados::updateorCreate(
                                    [
                                        'compra_id' => $compra->id,

                                    ],
                                    [
                                        'cod_documento' => $documentoMod['codTipoCDPMod'],
                                        'num_serie' => $documentoMod['numSerieCDPMod'],
                                        'num_documento' => $documentoMod['numCDPMod'],
                                        'fec_emision_mod' => $fechaEmision->format('Y-m-d') ?? null,
                                    ]
                                );
                            }
                        }
                    }

                    $results[] = [
                        'linea' => $index + 1,
                        'status' => 'success',
                        'data' => $compra,
                    ];
                } catch (\Exception $e) {
                    $errorLines[] = [
                        'linea' => $index + 1,
                        'errors' => ['error' => $e->getMessage()],
                    ];
                }
            }
        }

        // Si hubo errores en alguna línea, devolver un resumen detallado de los errores
        if (!empty($errorLines)) {
            return response()->json([
                'message' => 'Se encontraron errores en las siguientes líneas:',
                'error_lines' => $errorLines,
                'successful_lines' => $results, // Opcional: Incluir líneas procesadas correctamente
            ], 422);
        }

        // Si no hubo errores, devolver un mensaje de éxito
        return response()->json([
            'message' => 'Todos los registros se procesaron correctamente.',
            'results' => $results,
        ]);




        return response()->json(['message' => 'Compras guardadas correctamente'], 201);
    }

    // Obtener una compra específica
    public function show($id)
    {
        $compra = SireCompras::with(['tipoCambio', 'montos', 'documentosModificados', 'auditoria'])->findOrFail($id);
        return response()->json($compra);
    }

    // Actualizar una compra específica
    public function update(Request $request, $id)
    {
        $compra = SireCompras::findOrFail($id);

        $request->validate([
            'num_ruc' => 'sometimes|required',
            'nom_razon_social' => 'sometimes|required',
            'cod_car' => 'sometimes|required',
            'cod_tipo_cdp' => 'sometimes|required',
            'des_tipo_cdp' => 'sometimes|required',
            'num_serie_cdp' => 'sometimes|required',
            'num_cdp' => 'sometimes|required',
            'fec_emision' => 'sometimes|required|date',
            'fec_venc_pag' => 'nullable|date',
            'num_doc_identidad_proveedor' => 'sometimes|required',
            'nom_razon_social_proveedor' => 'sometimes|required',
            'cod_moneda' => 'sometimes|required',
            'mto_total_cp' => 'sometimes|required|numeric',
            'tipo_cambio' => 'sometimes|array',
            'montos' => 'sometimes|array',
            'lis_documentos_mod' => 'nullable|array',
            'auditoria' => 'sometimes|array',
        ]);

        // Actualizar la compra
        $compra->update($request->only([
            'num_ruc',
            'nom_razon_social',
            'cod_car',
            'cod_tipo_cdp',
            'des_tipo_cdp',
            'num_serie_cdp',
            'num_cdp',
            'fec_emision',
            'fec_venc_pag',
            'num_doc_identidad_proveedor',
            'nom_razon_social_proveedor',
            'cod_moneda',
            'mto_total_cp',
        ]));

        // Actualizar el tipo de cambio
        if ($request->has('tipo_cambio')) {
            $compra->tipoCambio()->update($request->input('tipo_cambio'));
        }

        // Actualizar los montos
        if ($request->has('montos')) {
            $compra->montos()->update($request->input('montos'));
        }

        // Actualizar los documentos modificados
        if ($request->has('lis_documentos_mod')) {
            $compra->documentosModificados()->delete(); // Eliminar los existentes
            foreach ($request->input('lis_documentos_mod') as $documentoMod) {
                $compra->documentosModificados()->create($documentoMod);
            }
        }

        // Actualizar la auditoría
        if ($request->has('auditoria')) {
            $compra->auditoria()->update($request->input('auditoria'));
        }

        return response()->json($compra->load(['tipoCambio', 'montos', 'documentosModificados', 'auditoria']));
    }
    /**
     * Lista las compras filtradas por el RUC de una empresa.
     */
    public function comprasPorRuc($ruc)
    {
        // Buscar las compras relacionadas con el RUC proporcionado
        $compras = SireCompras::where('num_ruc', $ruc)->orderBy('fec_emision', 'desc')->get();

        // Si no se encuentran compras, devolver un error 404
        if ($compras->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron compras para el RUC proporcionado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Compras recuperadas correctamente para el RUC: ' . $ruc,
            'data' => $compras,
        ], 200);
    }
    public function obtenerComprasPorPeriodo(Request $request)
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
            // Consultar las compras filtradas por periodo
            $compras = SireCompras::with(['tipoCambio', 'montos', 'documentosModificados', 'auditoria','archivo'])->select(
                'id',
                'num_doc_identidad_proveedor',
                'nom_razon_social_proveedor',
                'cod_tipo_cdp',
                'des_tipo_cdp',
                'num_serie_cdp',
                'num_cdp',
                'cod_moneda',
                'des_estado_comprobante',
                'cod_estado_comprobante',
                'fec_emision',
                'fec_venc_pag',
                'mto_total_cp'
            )->where('per_tributario', $perPeriodo)
                ->where('num_ruc', $perRuc)
                ->orderBy('fec_emision', 'desc')
                ->get();

            // Validar que existan compras para el periodo
            if ($compras->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron compras para el periodo proporcionado.',
                ], 404);
            }

            // Devolver los datos como JSON
            return response()->json([
                'message' => 'Compras obtenidas correctamente.',
                'data' => $compras,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarEstadoComprobante(Request $request)
    {
        try {
            // Validar que se hayan enviado los datos necesarios
            $request->validate([
                'id' => 'required|integer', // ID de la compra a actualizar
                'comprobante_estado_codigo' => 'required|string',
                'comprobante_estado_descripcion' => 'required|string',
            ]);

            // Obtener los datos del request
            $id = $request->input('id');
            $codigoEstado = $request->input('comprobante_estado_codigo');
            $descripcionEstado = $request->input('comprobante_estado_descripcion');

            // Buscar la compra por ID
            $compra = SireCompras::find($id);

            if (!$compra) {
                return response()->json([
                    'message' => 'No se encontró la compra con el ID proporcionado.',
                ], 404);
            }

            // Actualizar las columnas en la base de datos
            $compra->update([
                'cod_estado_comprobante' => $codigoEstado,
                'des_estado_comprobante' => $descripcionEstado,
            ]);

            // Devolver una respuesta exitosa
            return response()->json([
                'message' => 'Estado del comprobante actualizado correctamente.',
                'data' => [
                    'id' => $compra->id,
                    'cod_estado_comprobante' => $compra->cod_estado_comprobante,
                    'des_estado_comprobante' => $compra->des_estado_comprobante,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado del comprobante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // Eliminar una compra específica
    public function destroy($id)
    {
        $compra = SireCompras::findOrFail($id);
        $compra->delete();
        return response()->json(null, 204);
    }
    public function comprasPorProveedor(Request $request)
    {
        // Obtener los filtros de num_ruc y num_doc desde la solicitud
        $numRuc = $request->input('num_ruc');
        $numDoc = $request->input('num_doc');

        // Filtrar las compras por num_ruc y num_doc
        $compras = SireCompras::where('num_ruc', $numRuc)
            ->where('num_doc_identidad_proveedor', $numDoc)
            ->orderBy('fec_emision', 'desc')
            ->get();
        // Devolver los resultados en formato JSON
        return response()->json($compras);
    }
    public function ComprasPorMesporPro($num_ruc)
    {
        // Obtener las compras para un proveedor específico
        $compras = SireCompras::where('num_doc_identidad_proveedor', $num_ruc)
            ->whereYear('fec_emision', '>=', Carbon::now()->subYear()->year) // Filtramos por el año actual y el anterior
            ->whereYear('fec_emision', '<=', Carbon::now()->year) // Evitamos compras futuras
            ->get();

        // Inicializamos una estructura para agrupar los datos por año y mes
        $datosAgrupados = [];

        // Procesar las compras
        foreach ($compras as $compra) {
            $anio = Carbon::parse($compra->fec_emision)->year;
            $mes = (int) Carbon::parse($compra->fec_emision)->month; // Mes como número entero
            $monto = $compra->mto_total_cp;

            // Si el año no está en la estructura, lo inicializamos
            if (!isset($datosAgrupados[$anio])) {
                $datosAgrupados[$anio] = [];
            }

            // Si el mes no está en la estructura del año, lo inicializamos
            if (!isset($datosAgrupados[$anio][$mes])) {
                $datosAgrupados[$anio][$mes] = 0;
            }

            // Acumulamos el monto para el mes correspondiente
            $datosAgrupados[$anio][$mes] += $monto;
        }

        // Convertir la estructura agrupada al formato deseado
        $resultado = [];
        foreach ($datosAgrupados as $anio => $meses) {
            $lisMeses = [];
            foreach ($meses as $mes => $total) {
                $lisMeses[] = [
                    'mes' => $mes,
                    'total' => $total
                ];
            }

            // Agregamos el año y sus meses al resultado
            $resultado[] = [
                'anio' => $anio,
                'lisMeses' => $lisMeses
            ];
        }

        // Ordenar los años de forma descendente
        usort($resultado, function ($a, $b) {
            return $b['anio'] - $a['anio'];
        });

        // Devolver el JSON con el formato deseado
        return response()->json([
            'message' => 'Datos agrupados por año y mes.',
            'data' => $resultado
        ]);
    }

    public function faltantes(Request $request)
    {
        // Devuelve los IDs de las ventas que NO tienen ningún registro en ventas_archivos
        // Devuelve las ventas que NO tienen ningún registro en ventas_archivos
        $ventas = SireCompras::whereDoesntHave('archivo')
            ->select('id', 'cod_tipo_cdp', 'num_serie_cdp', 'num_cdp', 'num_doc_identidad_proveedor')
            ->get();

        return response()->json([
            'compras_sin_archivos' => $ventas
        ]);
    }
}
