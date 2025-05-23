<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Usamos HTTP Client de Laravel

class FactilizaController extends Controller
{
    public function getXml(Request $request)
    {
        // Validar los parámetros requeridos
        $request->validate([
            'numRuc' => 'required|string',
            'tipoDocumento' => 'required|string',
            'numSerieComprobante' => 'required|string',
            'numDocumentoComprobante' => 'required|string',
        ]);

        // Obtener parámetros
        $numRuc = $request->input('numRuc');
        $tipoDocumento = $request->input('tipoDocumento');
        $numSerieComprobante = $request->input('numSerieComprobante');
        $numDocumentoComprobante = $request->input('numDocumentoComprobante');

        // Construir URL de Factiliza
        $url = "https://api.factiliza.com/v1/sunat/xml/{$numRuc}-{$tipoDocumento}-{$numSerieComprobante}-{$numDocumentoComprobante}";

        // Tu token de Factiliza (guárdalo de preferencia en .env)
        $token = env('FACTILIZA_TOKEN');
        // Si no tienes el token en .env, puedes usar el siguiente código para obtenerlo


        // Consumir API de Factiliza
        $response = Http::withToken($token)->get($url);

        // Devolver respuesta (puedes adaptarla según tu necesidad)
        if ($response->successful()) {
            // Si quieres devolver el XML puro
            return response($response->body(), 200)
                ->header('Content-Type', $response->header('Content-Type'));
        } else {
            return response()->json([
                'error' => 'Error consultando Factiliza',
                'message' => $response->body()
            ], $response->status());
        }
    }
}
