<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;

class UserEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los usuarios con sus empresas relacionadas
        $users = User::with('empresas:id,nombre,ruc')->paginate(15);

        // Transformar los datos para agrupar por usuario
        // Nota: La paginación devuelve un LengthAwarePaginator, accedemos a los items con $users->items()
        $groupedData = collect($users->items())->map(function ($user) {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'empresas' => $user->empresas->map(function ($empresa) {
                    return [
                        'empresa_id' => $empresa->id,
                        'empresa_nombre' => $empresa->nombre, // Cambiado de razon_social a nombre
                        'ruc' => $empresa->ruc, // Añadido ruc si está disponible
                        'created_at' => $empresa->pivot->created_at,
                        'updated_at' => $empresa->pivot->updated_at,
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'Relaciones entre usuarios y empresas agrupadas correctamente.',
            'data' => $groupedData,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Verifica que el user_id exista en la tabla users
            'empresa_id' => 'required|exists:empresas,id', // Verifica que el empresa_id exista en la tabla empresas
        ]);

        // Obtener el usuario y la empresa
        $user = User::findOrFail($validated['user_id']);
        $empresa = Empresa::findOrFail($validated['empresa_id']);

        // Verificar si la relación ya existe
        if ($user->empresas()->where('empresa_id', $validated['empresa_id'])->exists()) {
            return response()->json([
                'message' => 'La empresa ya está asignada al usuario.',
            ], 409); // Código 409: Conflict
        }

        // Asignar la empresa al usuario
        $user->empresas()->attach($validated['empresa_id']);

        return response()->json([
            'message' => 'Empresa asignada correctamente al usuario.',
            'data' => [
                'user_id' => $validated['user_id'],
                'empresa_id' => $validated['empresa_id'],
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($userId)
    {
        // Buscar el usuario por su ID y cargar sus empresas relacionadas
        $user = User::with('empresas:id,nombre,ruc')->find($userId);

        // Si no se encuentra el usuario, devolver un error 404
        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        // Transformar los datos de las empresas relacionadas
        $empresas = $user->empresas->map(function ($empresa) {
            return [
                'empresa_id' => $empresa->id,
                'ruc' => $empresa->ruc,
                'empresa_nombre' => $empresa->nombre, // Asegurado que se usa nombre
                'created_at' => $empresa->pivot->created_at,
                'updated_at' => $empresa->pivot->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Empresas asignadas al usuario recuperadas correctamente.',
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'empresas' => $empresas,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
