<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use App\Models\TablaGeneral\Anexo;
use App\Models\Empresa;
use App\Models\TablaGeneral\TipoDocumentoIdentidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Policies\AnexoPolicy;
use Illuminate\Support\Facades\Validator;

class AnexoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'sometimes|string|max:255',
                'tipo_anexo' => 'sometimes|in:C,P',
                'estado' => 'sometimes|boolean',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'page' => 'sometimes|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Anexo::with(['empresa', 'tipoDocumentoIdentidad'])
                ->where('id_empresa', $request->header('X-Empresa-ID')); // ID de empresa desde header

            // Búsqueda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('documento', 'like', "%{$search}%")
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('codigo', 'like', "%{$search}%");
                });
            }

            // Filtros
            if ($request->has('tipo_anexo')) {
                $query->where('tipo_anexo', $request->tipo_anexo);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            $perPage = $request->per_page ?? 15;
            $anexos = $query->orderBy('nombre')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $anexos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los anexos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tipo_anexo' => 'required|in:C,P',
                'id_tipo_documento_identidad' => 'required|exists:tipo_documento_identidad,id',
                'codigo' => 'required|string|max:1',
                'documento' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('anexos')->where(function ($query) use ($request) {
                        return $query->where('id_empresa', $request->header('X-Empresa-ID'))
                            ->where('id_tipo_documento_identidad', $request->id_tipo_documento_identidad);
                    })
                ],
                'nombre' => 'required|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:100',
                'contacto' => 'nullable|string|max:255',
                'ag_retencion' => 'nullable|boolean',
                'ag_percepcion' => 'nullable|boolean',
                'buen_contribuyente' => 'nullable|boolean',
                'estado' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validación adicional para RUC/DNI
            $tipoDoc = TipoDocumentoIdentidad::find($request->id_tipo_documento_identidad);
            if ($tipoDoc->codigo === '6' && !preg_match('/^\d{11}$/', $request->documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El RUC debe tener 11 dígitos'
                ], 422);
            } elseif ($tipoDoc->codigo === '1' && !preg_match('/^\d{8}$/', $request->documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El DNI debe tener 8 dígitos'
                ], 422);
            }

            $anexoData = $validator->validated();
            $anexoData['id_empresa'] = $request->header('X-Empresa-ID');

            $anexo = Anexo::create($anexoData);

            return response()->json([
                'success' => true,
                'message' => 'Anexo creado exitosamente',
                'data' => $anexo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el anexo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $anexo = Anexo::with(['empresa', 'tipoDocumentoIdentidad'])
                ->where('id_empresa', request()->header('X-Empresa-ID'))
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $anexo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Anexo no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $anexo = Anexo::where('id_empresa', $request->header('X-Empresa-ID'))
                ->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tipo_anexo' => 'sometimes|in:C,P',
                'id_tipo_documento_identidad' => 'sometimes|exists:tipo_documento_identidad,id',
                'codigo' => 'sometimes|string|max:1',
                'documento' => [
                    'sometimes',
                    'string',
                    'max:20',
                    Rule::unique('anexos')->ignore($anexo->id)->where(function ($query) use ($request) {
                        return $query->where('id_empresa', $request->header('X-Empresa-ID'))
                            ->where('id_tipo_documento_identidad', $request->id_tipo_documento_identidad ?? $anexo->id_tipo_documento_identidad);
                    })
                ],
                'nombre' => 'sometimes|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:100',
                'contacto' => 'nullable|string|max:255',
                'ag_retencion' => 'nullable|boolean',
                'ag_percepcion' => 'nullable|boolean',
                'buen_contribuyente' => 'nullable|boolean',
                'estado' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validación adicional para RUC/DNI si se actualiza el documento
            if ($request->has('documento')) {
                $tipoDocId = $request->id_tipo_documento_identidad ?? $anexo->id_tipo_documento_identidad;
                $tipoDoc = TipoDocumentoIdentidad::find($tipoDocId);

                if ($tipoDoc->codigo === '6' && !preg_match('/^\d{11}$/', $request->documento)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El RUC debe tener 11 dígitos'
                    ], 422);
                } elseif ($tipoDoc->codigo === '1' && !preg_match('/^\d{8}$/', $request->documento)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El DNI debe tener 8 dígitos'
                    ], 422);
                }
            }

            $anexo->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Anexo actualizado exitosamente',
                'data' => $anexo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el anexo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $anexo = Anexo::where('id_empresa', request()->header('X-Empresa-ID'))
                ->findOrFail($id);

            // Verificar si tiene registros asociados
            if ($anexo->compras()->exists() || $anexo->ventas()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el anexo porque tiene registros asociados'
                ], 422);
            }

            $anexo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anexo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el anexo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del anexo
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'estado' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $anexo = Anexo::where('id_empresa', $request->header('X-Empresa-ID'))
                ->findOrFail($id);

            $anexo->update(['estado' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del anexo actualizado',
                'data' => $anexo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del anexo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener anexos por tipo
     */
    public function getAnexosPorTipo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tipo_anexo' => 'required|in:C,P',
                'estado' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Anexo::where('id_empresa', $request->header('X-Empresa-ID'))
                ->where('tipo_anexo', $request->tipo_anexo);

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            $anexos = $query->orderBy('nombre')
                ->get(['id', 'documento', 'nombre']);

            return response()->json([
                'success' => true,
                'data' => $anexos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los anexos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar anexo por documento
     */
    public function buscarPorDocumento(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'documento' => 'required|string',
                'tipo_anexo' => 'sometimes|in:C,P'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Anexo::where('id_empresa', $request->header('X-Empresa-ID'))
                ->where('documento', $request->documento);

            if ($request->has('tipo_anexo')) {
                $query->where('tipo_anexo', $request->tipo_anexo);
            }

            $anexo = $query->first();

            if (!$anexo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anexo no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $anexo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el anexo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
