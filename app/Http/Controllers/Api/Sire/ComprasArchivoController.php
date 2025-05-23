<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireComprasArchivo;

class ComprasArchivoController extends Controller
{
    public function index()
    {
        return SireComprasArchivo::all();
    }

    // Crear nuevo registro
    public function store(Request $request)
    {
        $validated = $request->validate([
            'compra_id' => 'required|integer|exists:sire_compras,id',
            'xml' => 'nullable', // aceptamos string O archivo
            'cdr' => 'nullable|file|mimes:zip',
            'pdf' => 'nullable|file|mimes:pdf',
            'guia' => 'nullable|file|mimes:pdf,xml,zip',
        ]);

        $paths = [];

        // Guardar XML si viene como archivo adjunto
        if ($request->hasFile('xml')) {
            /* $paths['xml'] = $request->file('xml')->store('venta_archivos/xml', 'public'); */
            $paths['xml'] = $request->file('xml')->storeAs('compra_archivos/xml', $request->file('xml')->getClientOriginalName(), 'public');
        }

        // Guardar otros archivos igual que antes
        foreach (['cdr', 'pdf', 'guia'] as $field) {
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store("compra_archivos/$field", 'public');
            }
        }

        $archivo = SireComprasArchivo::create(array_merge(
            ['compra_id' => $validated['compra_id']],
            $paths
        ));

        return response()->json($archivo, 201);
    }

    // Mostrar un archivo específico
    public function show($id)
    {
        $archivo = SireComprasArchivo::findOrFail($id);
        return response()->json($archivo);
    }

    // Actualizar un archivo existente
    public function update(Request $request, $id)
    {
        $archivo = SireComprasArchivo::findOrFail($id);

        $validated = $request->validate([
            'compra_id' => 'sometimes|integer|exists:sire_compras,id',
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
        $archivo = SireComprasArchivo::findOrFail($id);
        $archivo->delete();

        return response()->json(null, 204);
    }
}
