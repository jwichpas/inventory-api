<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'id_empresa' => $this->id_empresa,
            'id_brand' => $this->id_brand,
            'codigo' => $this->codigo,
            'name' => $this->name,
            'description' => $this->description,
            'imagen' => $this->imagen,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'empresa' => [
                'id' => $this->empresa->id,
                'name' => $this->empresa->name
            ],
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name
            ],
            'categorias' => $this->categorias->map(function ($categoria) {
                return [
                    'id' => $categoria->id,
                    'name' => $categoria->name,
                    'pivot' => [
                        'id_producto' => $categoria->pivot->id_producto,
                        'id_categoria' => $categoria->pivot->id_categoria
                    ]
                ];
            }),
            /* 'variantes' => $this->variantes->map(function ($variante) {
                // Organizar atributos por tipo
                $atributosOrganizados = [];
                $colores = [];

                foreach ($variante->atributos as $atributo) {
                    $tipo = strtolower($atributo->tipoAtributo->name);

                    if ($tipo === 'color') {
                        $colores[] = $atributo->valor;
                    } else {
                        $atributosOrganizados[$tipo] = $atributo->valor;
                    }
                }

                if (!empty($colores)) {
                    $atributosOrganizados['colores'] = $colores;
                }

                return array_merge($variante->toArray(), $atributosOrganizados);
            }) */
            'variantes' => $this->variantes->map(function ($variante) {
                $atributosTransformados = [];

                // Primero agrupamos los atributos por su tipo
                $atributosAgrupados = $variante->atributos->groupBy(function ($item) {
                    return strtolower($item->tipoAtributo->name);
                });

                // Luego procesamos cada grupo
                foreach ($atributosAgrupados as $tipo => $atributos) {
                    // Si hay múltiples valores para el mismo tipo, lo mantenemos como array
                    if ($atributos->count() > 1) {
                        $atributosTransformados[$tipo] = $atributos->pluck('valor')->toArray();
                    } else {
                        // Si solo hay un valor, lo ponemos directamente
                        $atributosTransformados[$tipo] = $atributos->first()->valor;
                    }
                }

                return array_merge($variante->toArray(), $atributosTransformados);
            })

        ];
    }
}
