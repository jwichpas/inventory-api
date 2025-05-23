<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireVentasArchivo;
use Illuminate\Support\Facades\Storage;

class VentasArchivoController extends Controller
{
    public function index()
    {
        return SireVentasArchivo::all();
    }

    // Crear nuevo registro
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venta_id' => 'required|integer|exists:sire_ventas,id',
            'xml' => 'nullable', // aceptamos string O archivo
            'cdr' => 'nullable|file|mimes:zip',
            'pdf' => 'nullable|file|mimes:pdf',
            'guia' => 'nullable|file|mimes:pdf,xml,zip',
        ]);

        $paths = [];

        // Guardar XML si viene como archivo adjunto
        if ($request->hasFile('xml')) {
            /* $paths['xml'] = $request->file('xml')->store('venta_archivos/xml', 'public'); */
            $paths['xml'] = $request->file('xml')->storeAs('venta_archivos/xml', $request->file('xml')->getClientOriginalName(), 'public');
        }

        // Guardar otros archivos igual que antes
        foreach (['cdr', 'pdf', 'guia'] as $field) {
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store("venta_archivos/$field", 'public');
            }
        }

        $archivo = SireVentasArchivo::create(array_merge(
            ['venta_id' => $validated['venta_id']],
            $paths
        ));

        return response()->json($archivo, 201);
    }

    // Mostrar un archivo específico
    public function show($id)
    {
        $archivo = SireVentasArchivo::findOrFail($id);
        return response()->json($archivo);
    }

    // Actualizar un archivo existente
    public function update(Request $request, $id)
    {
        $archivo = SireVentasArchivo::findOrFail($id);

        $validated = $request->validate([
            'venta_id' => 'sometimes|integer|exists:ventas,id',
            'xml' => 'nullable|string|max:255',
            'cdr' => 'nullable|string|max:255',
            'pdf' => 'nullable|string|max:255',
            'guia' => 'nullable|string|max:255',
        ]);

        $archivo->update($validated);

        return response()->json($archivo);
    }

    // Eliminar un archivo
    public function destroy($id)
    {
        $archivo = SireVentasArchivo::findOrFail($id);
        $archivo->delete();

        return response()->json(null, 204);
    }
}
