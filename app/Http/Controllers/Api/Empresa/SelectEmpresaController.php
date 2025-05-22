<?php

namespace App\Http\Controllers\Api\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa\SeleccionEmpresa;

class SelectEmpresaController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'empresa_id' => 'required|exists:empresas,id', // Verifica que el ID exista en la tabla empresas
            ]);

            // Obtener el ID del usuario autenticado (opcional)

            // Guardar la selección de la empresa
            $seleccion = SeleccionEmpresa::updateOrCreate(
                ['user_id' => $request->user_id], // Clave única para evitar duplicados
                ['empresa_id' => $request->empresa_id]
            );

            return response()->json([
                'message' => 'Empresa seleccionada guardada correctamente.',
                'data' => $seleccion,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar la selección de la empresa.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Obtener el ID del usuario autenticado (opcional)

            // Buscar la selección de la empresa
            $seleccion = SeleccionEmpresa::where('user_id', $id)->first();

            if (!$seleccion) {
                return response()->json([
                    'message' => 'No se encontró ninguna empresa seleccionada.',
                ], 404);
            }

            return response()->json([
                'message' => 'Empresa seleccionada recuperada correctamente.',
                'data' => $seleccion,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al recuperar la selección de la empresa.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
