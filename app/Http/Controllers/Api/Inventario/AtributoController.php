<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Atributos;
use Illuminate\Support\Facades\Validator;

class AtributoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Atributos::with(['empresa', 'tipoAtributo']);

        // Filtros
        if ($request->has('id_empresa')) {
            $query->where('id_empresa', $request->id_empresa);
        }

        if ($request->has('id_tipo')) {
            $query->where('id_tipo', $request->id_tipo);
        }

        if ($request->has('search')) {
            $query->where('valor', 'like', '%' . $request->search . '%');
        }

        // Ordenación
        $orderBy = $request->has('order_by') ? $request->order_by : 'valor';
        $orderDirection = $request->has('order_direction') ? $request->order_direction : 'asc';
        $query->orderBy($orderBy, $orderDirection);

        // Paginación
        $perPage = $request->has('per_page') ? (int)$request->per_page : 15;
        $atributos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $atributos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'id_tipo' => 'required|exists:tipo_atributos,id',
            'valor' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $atributo = Atributos::create([
                'id_empresa' => $request->id_empresa,
                'id_tipo' => $request->id_tipo,
                'valor' => $request->valor
            ]);

            // Cargar relaciones para la respuesta
            $atributo->load(['empresa', 'tipoAtributo']);

            return response()->json([
                'success' => true,
                'data' => $atributo,
                'message' => 'Atributo creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $atributo = Atributos::with(['empresa', 'tipoAtributo'])->find($id);

        if (!$atributo) {
            return response()->json([
                'success' => false,
                'message' => 'Atributo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $atributo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $atributo = Atributos::find($id);

        if (!$atributo) {
            return response()->json([
                'success' => false,
                'message' => 'Atributo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_tipo' => 'sometimes|required|exists:tipo_atributos,id',
            'valor' => 'sometimes|required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->has('id_tipo')) {
                $atributo->id_tipo = $request->id_tipo;
            }

            if ($request->has('valor')) {
                $atributo->valor = $request->valor;
            }

            $atributo->save();
            $atributo->refresh()->load(['empresa', 'tipoAtributo']);

            return response()->json([
                'success' => true,
                'data' => $atributo,
                'message' => 'Atributo actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $atributo = Atributos::find($id);

        if (!$atributo) {
            return response()->json([
                'success' => false,
                'message' => 'Atributo no encontrado'
            ], 404);
        }

        try {
            $atributo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Atributo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener atributos por tipo
     */
    public function porTipo($idTipo)
    {
        $atributos = Atributos::where('id_tipo', $idTipo)
            ->orderBy('valor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atributos
        ]);
    }
}
