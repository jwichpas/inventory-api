<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
<<<<<<< HEAD
            /* 'empresa_id' => 'required|exists:empresas,id' */
=======
            'empresa_id' => 'required|exists:empresas,id'
>>>>>>> develop
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
<<<<<<< HEAD
            /* 'empresa_id' => $validated['empresa_id'] */
=======
            'empresa_id' => $validated['empresa_id']
>>>>>>> develop
        ]);

        // Asignar rol por defecto (ajusta según tu sistema de roles)
        $user->assignRole('user');

<<<<<<< HEAD
        /* $token = $user->createTokenForEmpresa($validated['empresa_id']); */
        $token = $user->createTokenForEmpresa('auth_token');
=======
        $token = $user->createTokenForEmpresa($validated['empresa_id']);
>>>>>>> develop

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
<<<<<<< HEAD
            /* 'empresa_id' => 'required|exists:empresas,id' */
        ]);

        $user = User::where('email', $request->email)
=======
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $user = User::where('email', $request->email)
            ->where('id_empresa', $request->empresa_id)
>>>>>>> develop
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Revocar todos los tokens anteriores
        $user->tokens()->delete();

        $token = $user->createTokenForEmpresa($request->empresa_id);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('empresa', 'roles.permissions');

        return response()->json($user);
    }

    public function switchEmpresa(Request $request, Empresa $empresa)
    {
        $user = $request->user();

        if (!$user->empresas()->where('empresas.id', $empresa->id)->exists()) {
            return response()->json(['message' => 'No tienes acceso a esta empresa'], 403);
        }

        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        // Crear nuevo token para la empresa seleccionada
        $token = $user->createTokenForEmpresa($empresa->id);

        return response()->json([
            'user' => $user->load('empresa', 'roles.permissions'),
            'token' => $token->plainTextToken
        ]);
    }
}
