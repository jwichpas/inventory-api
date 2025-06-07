<?php

namespace App\Http\Controllers\Api\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EmpresaDataController extends Controller
{
    public function updateEmpresaData(Request $request, $empresaId)
    {
        $validated = $this->validateRequest($request);

        // Verificar que la empresa existe
        $empresa = Empresa::findOrFail($empresaId);

        DB::transaction(function () use ($empresa, $validated) {
            // Actualizar condiciones
            $this->updateCondiciones($empresa, $validated['condiciones']);

            // Actualizar tributos
            $this->updateTributos($empresa, $validated['tributos']);
        });

        return response()->json([
            'message' => 'Datos de la empresa actualizados correctamente',
            'data' => $empresa->load(['condiciones', 'tributos.exoneracion'])
        ], 200);
    }
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'numRuc' => 'sometimes|string|max:11',
            'desNombre' => 'sometimes|string|max:255',
            'numRegistros' => 'sometimes|string|max:10',
            'condiciones' => 'required|array',
            'condiciones.codDomHabido' => 'required|string|max:2',
            /* 'condiciones.periodo' => 'required|string|max:6', */
            'condiciones.codEstado' => 'required|string|max:2',
            'condiciones.fecAlta' => 'required|date',
            'condiciones.codDoble' => 'required|string|max:2',
            'condiciones.codMclase' => 'required|string|max:2',
            'condiciones.codReacti' => 'required|string|max:1',
            'tributos' => 'required|array',
            'tributos.*.codTributo' => 'required|string|max:6',
            /* 'tributos.*.periodo' => 'required|string|max:6', */
            'tributos.*.fecVigencia' => 'required|date',
            'tributos.*.fecAlta' => 'required|date',
            'tributos.*.codSisPag' => 'required|string|max:10',
            'tributos.*.codFrePago' => 'required|string|max:10',
            'tributos.*.codPerVsp' => 'required|string|max:10',
            'tributos.*.mtoImpMin' => 'required|numeric',
            'tributos.*.codGesMin' => 'required|string|max:10',
            'tributos.*.indAlta' => 'required|string|max:1',
            'tributos.*.codTipIns' => 'required|string|max:1',
            'tributos.*.desConDis' => 'required|string|max:50',
            'tributos.*.exoneracion' => 'required|array',
            'tributos.*.exoneracion.codExoDis' => 'required|string|max:20',
        ]);
    }

    protected function updateCondiciones(Empresa $empresa, array $condiciones)
    {
        $periodoActual = now()->format('Ym');
        // Transformar nombres de campos a formato snake_case para la base de datos
        $condicionesData = [
            'cod_dom_habido' => $condiciones['codDomHabido'],
            'periodo' => $periodoActual,
            'cod_estado' => $condiciones['codEstado'],
            'fec_alta' => $condiciones['fecAlta'],
            'cod_doble' => $condiciones['codDoble'],
            'cod_mclase' => $condiciones['codMclase'],
            'cod_reacti' => $condiciones['codReacti'],
        ];

        $empresa->condiciones()->updateOrCreate(
            ['empresa_id' => $empresa->id],
            $condicionesData
        );
    }

    protected function updateTributos(Empresa $empresa, array $tributos)
    {
        // Primero eliminamos tributos que no vienen en el request
        $tributosActuales = $empresa->tributos()->pluck('cod_tributo')->toArray();
        $tributosRecibidos = collect($tributos)->pluck('codTributo')->toArray();
        $tributosAEliminar = array_diff($tributosActuales, $tributosRecibidos);

        if (!empty($tributosAEliminar)) {
            $empresa->tributos()->whereIn('cod_tributo', $tributosAEliminar)->delete();
        }
        // Obtener el periodo de la fecha de alta
        $periodoActual = now()->format('Ym');

        // Actualizar o crear tributos recibidos
        foreach ($tributos as $tributoData) {
            $tributo = $empresa->tributos()->updateOrCreate(
                [
                    'cod_tributo' => $tributoData['codTributo'],
                    'periodo' => $periodoActual
                ],
                [
                    'fec_vigencia' => $tributoData['fecVigencia'],
                    'fec_alta' => $tributoData['fecAlta'],
                    'cod_sis_pag' => $tributoData['codSisPag'],
                    'cod_fre_pago' => $tributoData['codFrePago'],
                    'cod_per_vsp' => $tributoData['codPerVsp'],
                    'mto_imp_min' => $tributoData['mtoImpMin'],
                    'cod_ges_min' => $tributoData['codGesMin'],
                    'ind_alta' => $tributoData['indAlta'],
                    'cod_tip_ins' => $tributoData['codTipIns'],
                    'des_con_dis' => $tributoData['desConDis']
                ]
            );

            // Actualizar exoneración
            $tributo->exoneracion()->updateOrCreate(
                ['tributo_id' => $tributo->id],
                ['cod_exo_dis' => $tributoData['exoneracion']['codExoDis']]
            );
        }
    }
    protected function getPeriodoFromDate($date): string
    {
        $carbonDate = Carbon::parse($date);
        return $carbonDate->format('Ym');
    }
}
