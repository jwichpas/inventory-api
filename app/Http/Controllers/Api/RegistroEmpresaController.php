<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistroEmpresaController extends Controller
{
    public function registrar(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nombre_empresa' => 'required|string|max:255',
            'nombre_usuario' => 'required|string|max:255',
<<<<<<< HEAD
            'ruc_empresa' => 'required|string|max:20|unique:empresas,ruc',
=======
            'ruc' => 'required|string|max:20|unique:empresas,ruc',
>>>>>>> develop
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Errores de validación',
                'errores' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Crear empresa
            $empresa = Empresa::create([
                'nombre' => $request->nombre_empresa,
<<<<<<< HEAD
                'ruc' => $request->ruc_empresa,
=======
                'ruc' => $request->ruc,
>>>>>>> develop
                'direccion' => $request->direccion_empresa,
            ]);

            // Crear usuario asociado a la empresa
            $usuario = User::create([
                'name' => $request->nombre_usuario,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
                'id_empresa' => $empresa->id,

            ]);
            $usuario->assignRole('admin');

            DB::commit();

            return response()->json([
                'mensaje' => 'Empresa y usuario creados correctamente',
                'usuario' => $usuario,
                'empresa' => $empresa,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'mensaje' => 'Error al registrar empresa y usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
