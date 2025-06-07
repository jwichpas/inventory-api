<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Obtener los usuarios (con paginación si es necesario)
        $users = User::query()
            ->orderBy('created_at', 'desc')
            ->get(); // o ->paginate() si necesitas paginación

        // Calcular estadísticas (ejemplo)
        $stats = [
            'total' => User::count(),
            'active' => User::where('active', true)->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
        ];

        // Estructurar la respuesta como en el frontend
        return response()->json([
            'data' => [
                'users' => $users,
                'stats' => $stats,
            ]
        ]);
    }
    /**
     * Obtener todos los usuarios activos
     */
    public function getActiveUsersOld()
    {
        $users = User::where('active', true)
            ->select(['id', 'name', 'email', 'created_at'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    public function getActiveUsers(Request $request)
    {
        $query = User::where('active', true)
            ->select(['id', 'name', 'email', 'created_at']);

        // Filtro por búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenación
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage()
            ]
        ]);
    }
}
