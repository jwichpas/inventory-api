<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireEjercicio;
use App\Models\Sire\SirePeriodo;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa\SeleccionEmpresa;

class PeriodoController extends Controller
{
    public function index()
    {
        // Obtener todos los ejercicios fiscales con sus relaciones
        /* $ejerciciosFiscales = ejercicios::with(['empresa', 'periodos'])->get(); */
        /* $ejerciciosFiscales = ejercicios::all(); */
        // Obtener todos los ejercicios fiscales con sus períodos relacionados
        $ejerciciosFiscales = ejercicios::with('periodos')
        ->get()
        ->orderBy('num_ejercicio')
        ->map(function ($ejercicio) {
            return [
                'numEjercicio' => $ejercicio->num_ejercicio,
                'desEstado' => $ejercicio->des_estado,
                'lisPeriodos' => $ejercicio->periodos->map(function ($periodo) {
                    return [
                        'perTributario' => $periodo->per_tributario,
                        'codEstado' => $periodo->cod_estado,
                        'desEstado' => $periodo->des_estado,
                    ];
                }),
            ];
        });
        return response()->json([
            'message' => 'Datos agrupados por ejercicio fiscal.',
            'data' => $ejerciciosFiscales,
        ], 200);

    }
    public function obtenerEjerciciosFiscales(Request $request)
    {
        try {
            // Obtener el empresa_id del query string
            $empresaId = $request->query('empresa_id');

            // Validar que se haya proporcionado el empresa_id
            if (!$empresaId) {
                return response()->json([
                    'message' => 'El parámetro "empresa_id" es requerido.',
                ], 400);
            }

            // Consultar los ejercicios fiscales filtrados por empresa_id
            $ejerciciosFiscales = SireEjercicio::with('sire_periodos')
                ->where('empresa_id', $empresaId) // Filtrar por empresa_id
                ->orderBy('num_ejercicio', 'desc')
                ->get()
                ->map(function ($ejercicio) {
                    return [
                        'numEjercicio' => $ejercicio->num_ejercicio,
                        'desEstado' => $ejercicio->des_estado,
                        'lisPeriodos' => $ejercicio->sire_periodos->map(function ($periodo) {
                            return [
                                'perTributario' => $periodo->per_tributario,
                                'codEstado' => $periodo->cod_estado,
                                'desEstado' => $periodo->des_estado,
                            ];
                        }),
                    ];
                });

            return response()->json([
                'message' => 'Datos agrupados por ejercicio fiscal.',
                'data' => $ejerciciosFiscales,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            '*.numEjercicio' => 'required|string',
            '*.desEstado' => 'required|string',
            '*.lisPeriodos' => 'required|array',
            '*.lisPeriodos.*.perTributario' => 'required|string',
            '*.lisPeriodos.*.codEstado' => 'required|string',
            '*.lisPeriodos.*.desEstado' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id_empresa = SeleccionEmpresa::where('id', 1)->first()->id;
        // Procesar los datos
        $data = $request->all();

        foreach ($data as $ejercicioData) {
            // Crear o actualizar el ejercicio fiscal
            $ejercicio = ejercicios::updateOrCreate(
                [
                    'empresa_id' => $id_empresa,

                ],
                [
                    'num_ejercicio' => $ejercicioData['numEjercicio'], // Incluir num_ejercicio en la cláusula WHERE
                    'des_estado' => $ejercicioData['desEstado'], // Datos a actualizar o crear
                ]
            );

            // Procesar los períodos tributarios
            foreach ($ejercicioData['lisPeriodos'] as $periodoData) {
                periodos::updateOrCreate(
                    [
                        'ejercicios_id' => $ejercicio->id,
                    ],
                    [
                        'per_tributario' => $periodoData['perTributario'],
                        'cod_estado' => $periodoData['codEstado'],
                        'des_estado' => $periodoData['desEstado'],
                    ]
                );
            }
        }

        // Respuesta exitosa
        return response()->json([
            'message' => 'Datos recibidos y procesados correctamente.',
        ], 200);
    }
    public function guardar(Request $request,string $id)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            '*.numEjercicio' => 'required|string',
            '*.desEstado' => 'required|string',
            '*.lisPeriodos' => 'required|array',
            '*.lisPeriodos.*.perTributario' => 'required|string',
            '*.lisPeriodos.*.codEstado' => 'required|string',
            '*.lisPeriodos.*.desEstado' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Procesar los datos
        $data = $request->all();

        foreach ($data as $ejercicioData) {
            // Crear o actualizar el ejercicio fiscal
            $ejercicio = SireEjercicio::updateOrCreate(
                [
                    'empresa_id' => $id,
                    'num_ejercicio' => $ejercicioData['numEjercicio'], // Incluir num_ejercicio en la cláusula WHERE
                ],
                [
                    'des_estado' => $ejercicioData['desEstado'], // Datos a actualizar o crear
                ]
            );

            // Procesar los períodos tributarios
            foreach ($ejercicioData['lisPeriodos'] as $periodoData) {
                SirePeriodo::updateOrCreate(
                    [
                        'ejercicios_id' => $ejercicio->id,
                        'per_tributario' => $periodoData['perTributario'],
                    ],
                    [
                        'cod_estado' => $periodoData['codEstado'],
                        'des_estado' => $periodoData['desEstado'],
                    ]
                );
            }
        }

        // Respuesta exitosa
        return response()->json([
            'message' => 'Datos recibidos y procesados correctamente.',
        ], 200);
    }
    public function show($id)
    {
        // Buscar el ejercicio fiscal por ID con sus relaciones
        $ejercicioFiscal = ejercicios::with(['empresa', 'periodos'])->find($id);

        // Si no se encuentra el registro, devolver un error 404
        if (!$ejercicioFiscal) {
            return response()->json([
                'message' => 'Ejercicio fiscal no encontrado.',
            ], 404);
        }

        // Devolver los datos como respuesta JSON
        return response()->json([
            'message' => 'Ejercicio fiscal recuperado correctamente.',
            'data' => $ejercicioFiscal,
        ], 200);
    }
    public function update(Request $request, $id)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'num_ejercicio' => 'nullable|string',
            'des_estado' => 'nullable|string',
            'empresa_id' => 'nullable|exists:empresas,id',
            'lisPeriodos' => 'nullable|array',
            'lisPeriodos.*.per_tributario' => 'nullable|string',
            'lisPeriodos.*.cod_estado' => 'nullable|string',
            'lisPeriodos.*.des_estado' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buscar el ejercicio fiscal por ID
        $ejercicioFiscal = ejercicios::find($id);

        // Si no se encuentra el registro, devolver un error 404
        if (!$ejercicioFiscal) {
            return response()->json([
                'message' => 'Ejercicio fiscal no encontrado.',
            ], 404);
        }

        // Actualizar los campos del ejercicio fiscal
        $ejercicioFiscal->update([
            'num_ejercicio' => $request->input('num_ejercicio') ?? $ejercicioFiscal->num_ejercicio,
            'des_estado' => $request->input('des_estado') ?? $ejercicioFiscal->des_estado,
            'empresa_id' => $request->input('empresa_id') ?? $ejercicioFiscal->empresa_id,
        ]);

        // Procesar los períodos tributarios si se proporcionan
        if ($request->has('lisPeriodos')) {
            foreach ($request->input('lisPeriodos') as $periodoData) {
                ejercicios::updateOrCreate(
                    [
                        'ejercicio_fiscal_id' => $ejercicioFiscal->id,
                        'per_tributario' => $periodoData['per_tributario'],
                    ],
                    [
                        'cod_estado' => $periodoData['cod_estado'],
                        'des_estado' => $periodoData['des_estado'],
                    ]
                );
            }
        }

        // Devolver una respuesta exitosa
        return response()->json([
            'message' => 'Ejercicio fiscal actualizado correctamente.',
            'data' => $ejercicioFiscal->fresh()->load(['empresa', 'periodosTributarios']),
        ], 200);
    }
    public function destroy($id)
    {
        // Buscar el ejercicio fiscal por ID
        $ejercicioFiscal = ejercicios::find($id);

        // Si no se encuentra el registro, devolver un error 404
        if (!$ejercicioFiscal) {
            return response()->json([
                'message' => 'Ejercicio fiscal no encontrado.',
            ], 404);
        }

        // Eliminar el registro
        $ejercicioFiscal->delete();

        // Devolver una respuesta exitosa
        return response()->json([
            'message' => 'Ejercicio fiscal eliminado correctamente.',
        ], 200);
    }
}
