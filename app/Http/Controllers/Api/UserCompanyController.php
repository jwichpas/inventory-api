<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\User;
use App\Models\UserCompanie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserCompanyController extends Controller
{
    /**
     * Obtener empresas asignadas a un usuario
     */
    public function getUserCompanies($userId)
    {
        $user = User::findOrFail($userId);

        $empresas = $user->empresas()->where('estado', true)->get();

        return response()->json([
            'success' => true,
            'data' => $empresas
        ]);
    }

    /**
     * Obtener usuarios asignados a una empresa
     */
    public function getCompanyUsers($empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);

        $users = $empresa->usuarios()->where('active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Asignar empresas a usuarios
     */
    public function assignCompanies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'empresas' => 'required|array',
            'empresas.*' => 'exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Eliminar asignaciones previas
            UserCompanie::where('user_id', $request->user_id)->delete();

            // Crear nuevas asignaciones
            $assignments = [];
            foreach ($request->empresas as $empresaId) {
                $assignments[] = [
                    'user_id' => $request->user_id,
                    'empresa_id' => $empresaId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            UserCompanie::insert($assignments);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Empresas asignadas correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar empresas'
            ], 500);
        }
    }

    /**
     * Eliminar asignación
     */
    public function removeAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        UserCompanie::where('user_id', $request->user_id)
            ->where('empresa_id', $request->empresa_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asignación eliminada correctamente'
        ]);
    }
}
