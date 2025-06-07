<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireComprasItem;
use Illuminate\Support\Facades\Validator;
use App\Models\Sire\SireCompras;

class ComprasItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = SireComprasItem::with('compra')->get();
        return response()->json($items);
    }

    public function getComprasItems(Request $request, $num_ruc)
    {
        $query = SireCompras::with(['items' => function ($query) use ($request) {
            // Filtro por nombre de ítem si se proporciona
            if ($request->filled('searchItem')) {
                $query->where('description', 'like', '%' . $request->input('searchItem') . '%');
            }
        }, 'DespatchAdvice'])
            ->where('num_ruc', $num_ruc)
            ->orderBy('fec_emision', 'desc');

        // Filtros de fecha opcionales
        if ($request->filled('fechaDesde')) {
            $query->whereDate('fec_emision', '>=', $request->input('fechaDesde'));
        }

        if ($request->filled('fechaHasta')) {
            $query->whereDate('fec_emision', '<=', $request->input('fechaHasta'));
        }

        // Búsqueda por documento/proveedor o número de guía
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('num_doc_identidad_proveedor', 'like', '%' . $searchTerm . '%')
                    ->orWhere('nom_razon_social_proveedor', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('DespatchAdvice', function ($q2) use ($searchTerm) {
                        $q2->where('numero_guia', 'like', '%' . $searchTerm . '%');
                    });
            });
        }
        // Solo compras que tienen items (si se está buscando por ítem)
        if ($request->filled('searchItem')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->input('searchItem') . '%');
            });
        }

        // Paginación
        $perPage = $request->input('perPage', 10);
        $compras = $query->paginate($perPage);

        return response()->json($compras);
    }
    /**
     * Store a newly created resource or update an existing one in storage.
     */
    public function storeold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'compra_id' => 'required|exists:sire_compras,id',
            'invoicedQuantity' => 'required|numeric',
            'unitCode' => 'required|string|max:10',
            'priceAmount' => 'required|numeric',
            'priceTypeCode' => 'nullable|string|max:10',
            'sellersId' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'itemClassCode' => 'nullable|string|max:10',
            'taxAmount' => 'nullable|numeric',
            'taxableAmount' => 'nullable|numeric',
            'taxExemptionReasonCode' => 'nullable|string|max:10',
            'percent' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Definir los campos para buscar un registro existente
        $searchFields = [
            'compra_id' => $request->compra_id,
            'description' => $request->description,
            // Puedes agregar más campos según tu lógica de negocio
        ];

        // Crear o actualizar el registro
        $item = SireComprasItem::updateOrCreate(
            $searchFields,
            $request->all()
        );

        return response()->json([
            'message' => 'Item creado/actualizado exitosamente',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Buscar la compra con sus items y proveedor, igual que en getComprasItems pero solo una compra
        $compra = SireCompras::with(['items'])
            ->where('id', $id)
            ->first();

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        return response()->json($compra);
    }

    public function store(Request $request)
    {
        // Validar que el input sea un array
        if (!is_array($request->all())) {
            return response()->json([
                'message' => 'El formato de entrada debe ser un array de items'
            ], 400);
        }

        $savedItems = [];
        $errors = [];

        foreach ($request->all() as $index => $itemData) {
            $validator = Validator::make($itemData, [
                'compra_id' => 'required|exists:sire_compras,id',
                'invoicedQuantity' => 'required|numeric',
                'unitCode' => 'required|string|max:10',
                'priceAmount' => 'required|numeric',
                'priceTypeCode' => 'nullable|string|max:10',
                'sellersId' => 'nullable|string|max:50',
                'description' => 'required|string',
                'itemClassCode' => 'nullable|string|max:20',
                //'itemClassCode' => 'nullable|array',
                //'itemClassCode._' => 'nullable|string|max:20', // Accede al valor dentro del objeto
                'taxableAmount' => 'nullable|numeric',
                'taxAmount' => 'nullable|numeric',
                'taxExemptionReasonCode' => 'nullable|string|max:10',
                'percent' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors();
                continue;
            }

            // Preparar los datos para el item
            $processedData = [
                'compra_id' => $itemData['compra_id'],
                'invoicedQuantity' => $itemData['invoicedQuantity'],
                'unitCode' => $itemData['unitCode'],
                'priceAmount' => $itemData['priceAmount'],
                'priceTypeCode' => $itemData['priceTypeCode'] ?? null,
                'sellersId' => $itemData['sellersId'] ?? null,
                'description' => $itemData['description'],
                'itemClassCode' => $itemData['itemClassCode']['_'] ?? null, // Extraer el valor del objeto
                'taxableAmount' => $itemData['taxableAmount'] ?? null,
                'taxAmount' => $itemData['taxAmount'] ?? null,
                'taxExemptionReasonCode' => $itemData['taxExemptionReasonCode'] ?? null,
                'percent' => $itemData['percent'] ?? null,
            ];

            // Definir criterios de búsqueda para updateOrCreate
            $searchCriteria = [
                'compra_id' => $itemData['compra_id'],
                'description' => $itemData['description']
            ];

            // Crear o actualizar el item
            $item = SireComprasItem::updateOrCreate(
                $searchCriteria,
                $processedData
            );

            $savedItems[] = $item;
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Algunos items tuvieron errores',
                'errors' => $errors,
                'saved_items' => $savedItems
            ], 207); // Código 207 Multi-Status
        }

        return response()->json([
            'message' => count($savedItems) . ' items procesados exitosamente',
            'data' => $savedItems
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = SireComprasItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'invoicedQuantity' => 'sometimes|numeric',
            'unitCode' => 'sometimes|string|max:10',
            'priceAmount' => 'sometimes|numeric',
            'priceTypeCode' => 'nullable|string|max:10',
            'sellersId' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'itemClassCode' => 'nullable|string|max:10',
            'taxAmount' => 'nullable|numeric',
            'taxableAmount' => 'nullable|numeric',
            'taxExemptionReasonCode' => 'nullable|string|max:10',
            'percent' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item->update($request->all());

        return response()->json([
            'message' => 'Item actualizado exitosamente',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = SireComprasItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item eliminado exitosamente']);
    }
}
