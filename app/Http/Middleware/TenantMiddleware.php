<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Empresa;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener empresa del token o del usuario autenticado
        $empresaId = $request->user()->currentAccessToken()->abilities['empresa_id']
            ?? $request->user()->empresa_id
            ?? $request->header('X-Empresa-ID');

        if (!$empresaId) {
            return response()->json(['error' => 'Empresa no especificada'], 400);
        }

        $empresa = \App\Models\Empresa::find($empresaId);

        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        // Verificar que el usuario tenga acceso a esta empresa
        if (
            $request->user()->empresa_id != $empresaId &&
            !$request->user()->empresas()->where('empresas.id', $empresaId)->exists()
        ) {
            return response()->json(['error' => 'No autorizado para esta empresa'], 403);
        }

        // Establecer la empresa actual
        app()->instance('empresa', $empresa);

        return $next($request);
    }
}
