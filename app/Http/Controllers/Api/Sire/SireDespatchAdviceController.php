<?php

namespace App\Http\Controllers\api\sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireComprasDespatchAdvice;
use App\Models\Sire\SireComprasDespatchAdviceDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SireDespatchAdviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $despatchAdvices = SireComprasDespatchAdvice::with('details')->get();
        return response()->json([
            'success' => true,
            'data' => $despatchAdvices
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación para la cabecera
        $validator = Validator::make($request->all(), [
            'id_compra' => 'required|integer|exists:sire_compras,id',
            'data.*.DespatchAdviceTypeCode' => 'required|string|max:20',
            'data.*.numero_guia' => 'required|string|max:50|unique:despatch_advices',
            'data.*.IssueDate' => 'required|date',
            'data.*.IssueTime' => 'nullable|date_format:H:i:s',
            'data.*.HandlingCode' => 'nullable|string|max:10',
            'data.*.HandlingInstructions' => 'nullable|string',
            'data.*.unitCode' => 'nullable|string|max:10',
            'data.*.GrossWeightMeasure' => 'nullable|numeric',
            'data.*.TransportModeCode' => 'nullable|string|max:10',
            'data.*.StartDate' => 'nullable|date',
            'data.*.DespatchSupplierPartyId' => 'required|string|max:50',
            'data.*.DespatchSupplierPartyName' => 'required|string|max:100',
            'data.*.DeliveryCustomerPartyId' => 'required|string|max:50',
            'data.*.DeliveryCustomerPartyName' => 'required|string|max:100',
            'data.*.CarrierPartyId' => 'nullable|string|max:50',
            'data.*.CarrierPartyName' => 'nullable|string|max:100',
            'data.*.DeliveryAddressId' => 'nullable|string|max:50',
            'data.*.DeliveryAddressLine' => 'nullable|string',
            'data.*.DespatchAddressId' => 'nullable|string|max:50',
            'data.*.DespatchAddressLine' => 'nullable|string',
            'xml_file' => 'required|file|mimes:xml|max:10240',

            'details.*.ItemIdentification' => 'required|string|max:50',
            'details.*.ItemDescription' => 'required|string|max:255',
            'details.*.DespatchLine' => 'required|string|max:20',
            'details.*.DeliveredQuantity' => 'required|numeric',
            'details.*.unitCode' => 'required|string|max:10',
            'details.*.GrossWeightMeasure' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // Procesar y guardar el archivo ZIP
        if ($request->hasFile('xml_file')) {
            $zipFile = $request->file('xml_file');
            $fileName = 'public/despatch_advices/' . Str::uuid() . '.' . $zipFile->getClientOriginalExtension();

            // Guardar el archivo en el almacenamiento local
            $path = Storage::disk('local')->put($fileName, file_get_contents($zipFile));

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el archivo XML'
                ], 500);
            }
        }

        // Crear la cabecera incluyendo la ruta del archivo
        $mainData = json_decode($request->input('data'), true) ?? [];
        $despatchAdviceData = array_merge(
            $mainData,
            ['id_compra' => $request->input('id_compra')],
            ['xml_file' => $fileName ?? null]
        );

        $despatchAdvice = SireComprasDespatchAdvice::create($despatchAdviceData);

        // Crear los detalles
        $details = json_decode($request->input('details'), true) ?? [];
        foreach ($details as $detail) {
            $despatchAdvice->details()->create($detail);
        }

        return response()->json([
            'success' => true,
            'data' => $despatchAdvice->load('details'),
            'zip_path' => $fileName
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $despatchAdvice = SireComprasDespatchAdvice::with('details')->find($id);

        if (!$despatchAdvice) {
            return response()->json([
                'success' => false,
                'message' => 'Guía de remisión no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $despatchAdvice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $despatchAdvice = SireComprasDespatchAdvice::find($id);

        if (!$despatchAdvice) {
            return response()->json([
                'success' => false,
                'message' => 'Guía de remisión no encontrada'
            ], 404);
        }

        // Validación para la cabecera
        $validator = Validator::make($request->all(), [
            'DespatchAdviceTypeCode' => 'sometimes|required|string|max:20',
            'numero_guia' => 'sometimes|required|string|max:50|unique:despatch_advices,numero_guia,' . $id . ',id_compra',
            'IssueDate' => 'sometimes|required|date',
            'IssueTime' => 'nullable|date_format:H:i:s',
            'HandlingCode' => 'nullable|string|max:10',
            'HandlingInstructions' => 'nullable|string',
            'unitCode' => 'nullable|string|max:10',
            'GrossWeightMeasure' => 'nullable|numeric',
            'TransportModeCode' => 'nullable|string|max:10',
            'StartDate' => 'nullable|date',
            'DespatchSupplierPartyId' => 'sometimes|required|string|max:50',
            'DespatchSupplierPartyName' => 'sometimes|required|string|max:100',
            'DeliveryCustomerPartyId' => 'sometimes|required|string|max:50',
            'DeliveryCustomerPartyName' => 'sometimes|required|string|max:100',
            'CarrierPartyId' => 'nullable|string|max:50',
            'CarrierPartyName' => 'nullable|string|max:100',
            'DeliveryAddressId' => 'nullable|string|max:50',
            'DeliveryAddressLine' => 'nullable|string',
            'DespatchAddressId' => 'nullable|string|max:50',
            'DespatchAddressLine' => 'nullable|string',
            'details' => 'sometimes|array|min:1',
            'details.*.id' => 'sometimes|exists:despatch_advice_details,id',
            'details.*.ItemIdentification' => 'sometimes|required|string|max:50',
            'details.*.ItemDescription' => 'sometimes|required|string|max:255',
            'details.*.DespatchLine' => 'sometimes|required|string|max:20',
            'details.*.DeliveredQuantity' => 'sometimes|required|numeric',
            'details.*.unitCode' => 'sometimes|required|string|max:10',
            'details.*.GrossWeightMeasure' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar la cabecera
        $despatchAdvice->update($request->except('details'));

        // Sincronizar los detalles
        if ($request->has('details')) {
            $currentDetails = $despatchAdvice->details->pluck('id')->toArray();
            $updatedDetails = [];

            foreach ($request->details as $detail) {
                if (isset($detail['id']) && in_array($detail['id'], $currentDetails)) {
                    // Actualizar detalle existente
                    $despatchAdvice->details()
                        ->where('id', $detail['id'])
                        ->update($detail);
                    $updatedDetails[] = $detail['id'];
                } else {
                    // Crear nuevo detalle
                    $newDetail = $despatchAdvice->details()->create($detail);
                    $updatedDetails[] = $newDetail->id;
                }
            }

            // Eliminar detalles que no están en la solicitud
            $despatchAdvice->details()
                ->whereNotIn('id', $updatedDetails)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'data' => $despatchAdvice->load('details')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $despatchAdvice = SireComprasDespatchAdvice::find($id);

        if (!$despatchAdvice) {
            return response()->json([
                'success' => false,
                'message' => 'Guía de remisión no encontrada'
            ], 404);
        }

        $despatchAdvice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guía de remisión eliminada correctamente'
        ]);
    }

    /**
     * Descargar el archivo ZIP asociado a una guía de remisión
     */
    public function downloadZip($id)
    {
        $despatchAdvice = SireComprasDespatchAdvice::find($id);

        if (!$despatchAdvice || !$despatchAdvice->zip_path) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);
        }

        if (!Storage::disk('local')->exists($despatchAdvice->zip_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no existe en el almacenamiento'
            ], 404);
        }

        return Storage::disk('local')->download($despatchAdvice->zip_path);
    }
}
