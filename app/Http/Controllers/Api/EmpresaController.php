<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::with(['condiciones', 'tributos'])->paginate(15);
        return response()->json($empresas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:empresas',
            'ruc' => 'required|string|max:20|unique:empresas',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'correo' => 'nullable|string|email|max:255',
            'usuario_sol' => 'nullable|string|max:255',
            'clave_sol' => 'nullable|string|max:255',
            'cliente_id' => 'nullable|string|max:255',
            'cliente_secret' => 'nullable|string|max:255',
            'token' => 'nullable|string|max:255',
            'usuario_afp' => 'nullable|string|max:255',
            'clave_afp' => 'nullable|string|max:255',
            'imagen' => 'nullable|string|max:255',
            'estado' => 'required|string|in:0,1',
            'regimen_tributario' => 'nullable|string|max:255',
            'regimen_t_desde' => 'nullable|date',
            'regimen_laboral' => 'nullable|string|max:255',
            'regimen_l_desde' => 'nullable|date',
            'sunarp_oficina' => 'nullable|string|max:255',
            'sunarp_partida' => 'nullable|string|max:255',
            'sunarp_dni_representante' => 'nullable|string|max:255',
            'sunarp_nombre_representante' => 'nullable|string|max:255',
            'sunarp_cargo_representante' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $empresa = Empresa::create($request->all());
        return response()->json($empresa, 201);
    }

    public function show($id)
    {
        $empresa = Empresa::with(['condiciones', 'tributos'])->find($id);
        return response()->json([
            'success' => true,
            'data' => $empresa
        ]);
    }
    public function datos($id)
    {
        $empresa = Empresa::select('id','ruc', 'clave_sol', 'usuario_sol', 'cliente_id', 'cliente_secret')
            ->find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
        // Hacer visibles los campos ocultos
        $empresa->makeVisible(['clave_sol', 'cliente_secret']);

        return response()->json([
            'success' => true,
            'data' => $empresa
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::find($id);
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:empresas,nombre,' . $empresa->id,
            'ruc' => 'required|string|max:20|unique:empresas,ruc,' . $empresa->id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'correo' => 'nullable|string|email|max:255',
            'usuario_sol' => 'nullable|string|max:255',
            'clave_sol' => 'nullable|string|max:255',
            'cliente_id' => 'nullable|string|max:255',
            'cliente_secret' => 'nullable|string|max:255',
            'token' => 'nullable|string|max:255',
            'usuario_afp' => 'nullable|string|max:255',
            'clave_afp' => 'nullable|string|max:255',
            'imagen' => 'nullable|string|max:255',
            'estado' => 'required|string|in:0,1',
            'regimen_tributario' => 'nullable|string|max:255',
            'regimen_t_desde' => 'nullable|date',
            'regimen_laboral' => 'nullable|string|max:255',
            'regimen_l_desde' => 'nullable|date',
            'sunarp_oficina' => 'nullable|string|max:255',
            'sunarp_partida' => 'nullable|string|max:255',
            'sunarp_dni_representante' => 'nullable|string|max:255',
            'sunarp_nombre_representante' => 'nullable|string|max:255',
            'sunarp_cargo_representante' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $empresa->update($request->all());
        return response()->json($empresa);
    }

    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        $empresa->delete();
        return response()->json(['message' => 'Empresa eliminada correctamente']);
    }

    /**
     * Obtener todas las empresas activas
     */
    public function getActiveCompaniesOld()
    {
        $empresas = Empresa::where('estado', true)
            ->select(['id', 'nombre', 'ruc', 'direccion'])
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $empresas
        ]);
    }
    public function getActiveCompanies(Request $request)
    {
        $query = Empresa::where('estado', true)
            ->select(['id', 'nombre', 'ruc', 'direccion']);

        // Filtro por búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('ruc', 'like', "%{$search}%");
            });
        }

        // Ordenación
        $sortField = $request->input('sort_field', 'nombre');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->input('per_page', 10);
        $empresas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $empresas->items(),
            'pagination' => [
                'total' => $empresas->total(),
                'per_page' => $empresas->perPage(),
                'current_page' => $empresas->currentPage(),
                'last_page' => $empresas->lastPage()
            ]
        ]);
    }

    /**
     * Actualizar o guardar el token de una empresa
     */
    public function updateToken(Request $request, $id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['success' => false, 'message' => 'Empresa no encontrada'], 404);
        }
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:3000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $empresa->token = $request->input('token');
        $empresa->save();
        return response()->json([
            'success' => true,
            'message' => 'Token actualizado correctamente',
            'data' => $empresa
        ]);
    }

    /**
     * Consultar solo el token de una empresa
     */
    public function getToken($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['success' => false, 'message' => 'Empresa no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'token' => $empresa->token
        ]);
    }
}
