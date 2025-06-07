<?php

namespace App\Http\Controllers\Api\Factiliza;
/* namespace App\Http\Controllers\Api\Inventario; */

use App\Http\Controllers\Controller;
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
        $token = env('TOKEN_FACTILIZA');
        // Si no tienes el token en .env, puedes usar el siguiente código para obtenerlo


        // Consumir API de Factiliza
        $response = Http::withToken($token)->get($url);

        // Devolver respuesta (puedes adaptarla según tu necesidad)
        if ($response->successful()) {
            $json = $response->json();

            // Verificamos que existe el campo data
            if (isset($json['data'])) {
                $zipData = base64_decode($json['data']);

                // Guardamos temporalmente el archivo ZIP
                $tmpZip = tempnam(sys_get_temp_dir(), 'factiliza_zip_');
                file_put_contents($tmpZip, $zipData);

                $zip = new \ZipArchive;
                if ($zip->open($tmpZip) === TRUE) {
                    // Asumimos que solo hay un archivo XML dentro del ZIP
                    $xmlContent = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'xml') {
                            $xmlContent = $zip->getFromIndex($i);
                            break;
                        }
                    }
                    $zip->close();
                    unlink($tmpZip); // Eliminar el archivo temporal

                    if ($xmlContent) {
                        // Retornar el XML como texto
                        return response($xmlContent, 200)
                            ->header('Content-Type', 'application/xml');
                    } else {
                        return response()->json([
                            'error' => 'No se encontró archivo XML en el ZIP.'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'error' => 'No se pudo abrir el archivo ZIP.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => 'No se encontró el campo data en la respuesta.'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Error consultando Factiliza',
                'message' => $response->body()
            ], $response->status());
        }
    }
    public function getPdf(Request $request)
    {
        $request->validate([
            'numRuc' => 'required|string',
            'tipoDocumento' => 'required|string',
            'numSerieComprobante' => 'required|string',
            'numDocumentoComprobante' => 'required|string',
        ]);

        $numRuc = $request->input('numRuc');
        $tipoDocumento = $request->input('tipoDocumento');
        $numSerieComprobante = $request->input('numSerieComprobante');
        $numDocumentoComprobante = $request->input('numDocumentoComprobante');

        $url = "https://api.factiliza.com/v1/sunat/pdf/{$numRuc}-{$tipoDocumento}-{$numSerieComprobante}-{$numDocumentoComprobante}";
        $token = env('TOKEN_FACTILIZA');

        $response = Http::withToken($token)->get($url);

        if ($response->successful()) {
            $json = $response->json();

            if (isset($json['data'])) {
                // El PDF viene en base64, lo decodificamos
                $pdfData = base64_decode($json['data']);

                // Puedes devolver el PDF directamente
                return response($pdfData, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="documento.pdf"');
            } else {
                return response()->json([
                    'error' => 'No se encontró el campo data en la respuesta.'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Error consultando Factiliza',
                'message' => $response->body()
            ], $response->status());
        }
    }
    public function getGuiaXml(Request $request)
    {
        $request->validate([
            'numRuc' => 'required|string',
            'numSerieComprobante' => 'required|string',
            'numDocumentoComprobante' => 'required|string',
        ]);

        $numRuc = $request->input('numRuc');
        $numSerieComprobante = $request->input('numSerieComprobante');
        $numDocumentoComprobante = $request->input('numDocumentoComprobante');

        $url = "https://api.factiliza.com/v1/sunat/guia/xml/{$numRuc}-{$numSerieComprobante}-{$numDocumentoComprobante}";
        $token = env('TOKEN_FACTILIZA');

        $response = Http::withToken($token)->get($url);

        if ($response->successful()) {
            $json = $response->json();

            if (isset($json['data'])) {
                // El campo 'data' es un XML en base64, NO ZIP
                $xmlContent = base64_decode($json['data']);
                if ($xmlContent) {
                    return response($xmlContent, 200)
                        ->header('Content-Type', 'application/xml');
                } else {
                    return response()->json([
                        'error' => 'No se pudo decodificar el XML.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => 'No se encontró el campo data en la respuesta.'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Error consultando Factiliza',
                'message' => $response->body()
            ], $response->status());
        }
    }
    public function getGuiaXmlOld(Request $request)
    {
        $request->validate([
            'numRuc' => 'required|string',
            'numSerieComprobante' => 'required|string',
            'numDocumentoComprobante' => 'required|string',
        ]);

        $numRuc = $request->input('numRuc');
        $numSerieComprobante = $request->input('numSerieComprobante');
        $numDocumentoComprobante = $request->input('numDocumentoComprobante');

        $url = "https://api.factiliza.com/v1/sunat/guia/xml/{$numRuc}-{$numSerieComprobante}-{$numDocumentoComprobante}";
        $token = env('TOKEN_FACTILIZA');

        $response = Http::withToken($token)->get($url);

        if ($response->successful()) {
            $json = $response->json();

            if (isset($json['data'])) {
                $zipData = base64_decode($json['data']);

                $tmpZip = tempnam(sys_get_temp_dir(), 'factiliza_guia_zip_');
                file_put_contents($tmpZip, $zipData);

                $zip = new \ZipArchive;
                if ($zip->open($tmpZip) === TRUE) {
                    $xmlContent = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'xml') {
                            $xmlContent = $zip->getFromIndex($i);
                            break;
                        }
                    }
                    $zip->close();
                    unlink($tmpZip);

                    if ($xmlContent) {
                        return response($xmlContent, 200)
                            ->header('Content-Type', 'application/xml');
                    } else {
                        return response()->json([
                            'error' => 'No se encontró archivo XML en el ZIP.'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'error' => 'No se pudo abrir el archivo ZIP.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => 'No se encontró el campo data en la respuesta.'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Error consultando Factiliza',
                'message' => $response->body()
            ], $response->status());
        }
    }
}
