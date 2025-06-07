<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario\VarianteProduct;
use App\Http\Resources\Product\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])->paginate(20);
        return response()->json($products);
    }

    public function indexold(Request $request)
    {
        // Validar parámetros de consulta
        $validated = $request->validate([
            'search' => 'sometimes|string|max:100',
            'brand_id' => 'sometimes|integer|exists:brands,id',
            'category_id' => 'sometimes|integer|exists:categorias,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:name,created_at,updated_at',
            'sort_dir' => 'sometimes|string|in:asc,desc'
        ]);

        // Construir consulta base con eager loading
        $query = Product::with([
            'empresa:id,nombre',
            'brand:id,name',
            'categorias:id,name',
            'variantes' => function ($query) {
                $query->select([
                    'id',
                    'id_producto',
                    'sku',
                    'codigo_sunat',
                    'ean13',
                    'ean14',
                    'precio',
                    'id_unidad_medida'
                ]);
            },
            'variantes.atributos.tipoAtributo:id,name'
        ]);

        // Aplicar filtros
        if (!empty($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['search'] . '%')
                    ->orWhere('codigo', 'like', '%' . $validated['search'] . '%')
                    ->orWhere('description', 'like', '%' . $validated['search'] . '%');
            });
        }

        if (!empty($validated['brand_id'])) {
            $query->where('id_brand', $validated['brand_id']);
        }

        if (!empty($validated['category_id'])) {
            $query->whereHas('categorias', function ($q) use ($validated) {
                $q->where('id', $validated['category_id']);
            });
        }

        // Aplicar ordenamiento
        $sortBy = $validated['sort_by'] ?? 'name';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $query->orderBy($sortBy, $sortDir);

        // Paginación
        $perPage = $validated['per_page'] ?? 15;
        $products = $query->paginate($perPage);

        // Transformar resultados usando el Resource
        return ProductResource::collection($products)
            ->additional([
                'meta' => [
                    'filters' => $validated,
                    'sort' => [
                        'by' => $sortBy,
                        'direction' => $sortDir
                    ]
                ]
            ]);
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'id_empresa' => 'required|exists:empresas,id',
            'id_brand' => 'required|exists:brands,id',
            'id_category' => 'required|exists:categories,id',
            /* 'id_unidad_medida' => 'required|exists:unidads_medidas,id', */
            'codigo' => 'required|string|max:50|unique:products,codigo',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:100',
            'imagen' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'numeric|min:0',
            'slug' => 'nullable|string',
            'ean13' => 'nullable|string|max:255',
            'ean14' => 'nullable|string|max:255',
            'codigo-sunat' => 'nullable|string|max:255',
            'active' => 'boolean'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }
    public function guardarold(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'name' => 'required|string|max:100',
            'codigo' => 'required|string|max:50|unique:products,codigo,NULL,id,id_empresa,' . $request->id_empresa,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'id_brand' => 'nullable|exists:brands,id',
            'product_type' => 'required|in:simple,variable',

            // Campos para producto simple
            'sku' => 'required_if:product_type,simple|string|max:100',
            'price' => 'required_if:product_type,simple|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'unit_id' => 'required_if:product_type,simple|exists:unidad_medidas,id',
            'main_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',

            // Campos para producto variable
            'variants' => 'required_if:product_type,variable|array|min:1',
            'variants.*.sku' => 'required|string|max:100',
            'variants.*.sunat_code' => 'nullable|string|max:13',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'nullable|numeric|min:0',
            'variants.*.unit_id' => 'required|exists:unidad_medidas,id',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.attribute_id' => 'required|exists:atributos,id',
            /* 'variants.*.attributes.*.value_id' => 'required|exists:atributos_variante,id', */
            'variants.*.attributes.*.value_id' => 'required',

            /* 'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id', */
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Crear producto principal
            $productData = $request->only([
                'id_empresa',
                'name',
                'codigo',
                'description',
                'category_id',
                'id_brand',
                'product_type'
            ]);

            // Si es producto simple, agregar campos adicionales
            if ($request->product_type === 'simple') {
                $productData['sku'] = $request->sku;
                $productData['price'] = $request->price;
                $productData['stock'] = $request->stock;
                $productData['unit_id'] = $request->unit_id;
            }

            $product = Product::create($productData);
            // Asociar la categoría (o categorías) al producto
            $product->categorias()->attach($request->category_id);
            // Manejar imágenes para producto simple
            if ($request->product_type === 'simple') {
                if ($request->hasFile('main_image')) {
                    $path = $request->file('main_image')->store('products');
                    $product->main_image = $path;
                    $product->save();
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $path = $image->store('products');
                        $product->images()->create(['path' => $path]);
                    }
                }
            }
            // Manejar variantes para producto variable
            else {
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->create([
                        'id_producto' => $product->id,
                        'sku' => $variantData['sku'],
                        'codigo_sunat' => $variantData['sunat_code'],
                        'precio' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'id_unidad_medida' => $variantData['unit_id'],
                    ]);

                    // Asignar atributos a la variante
                    if (!empty($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attribute) {
                            $variant->attributes()->attach($attribute['attribute_id'], [
                                'value_id' => $attribute['value_id']
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $product->load(['empresa', 'brand', 'categorias', 'variantes'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'id_brand' => 'required|exists:brands,id',
            'codigo' => 'required|string|max:50',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:100',
            'imagen' => 'nullable|string|max:255',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::updateOrCreate(
            ['id_empresa' => $request->id_empresa, 'codigo' => $request->codigo],
            $request->except('categorias')
        );

        if ($request->has('categorias')) {
            $product->categorias()->sync($request->categorias);
        }

        return response()->json($product->load('categorias'), 201);
    }

    public function shownew($id)
    {
        $product = Product::with([
            'empresa:id,nombre',
            'brand:id,name',
            'categorias:id,name',
            'variantes.atributos.tipoAtributo'
        ])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Transformar las variantes con sus atributos organizados
        $product->variantes->transform(function ($variante) {
            $atributosOrganizados = [];

            foreach ($variante->atributos as $atributo) {
                $tipo = strtolower($atributo->tipoAtributo->name);

                if ($tipo === 'color') {
                    $atributosOrganizados['colores'][] = $atributo->valor;
                } else {
                    $atributosOrganizados[$tipo] = $atributo->valor;
                }
            }

            return array_merge($variante->toArray(), $atributosOrganizados);
        });

        return response()->json($product);
    }
    public function show($id)
    {
        $product = Product::with(['empresa', 'brand', 'category', 'unidadMedida'])->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'exists:empresas,id',
            'id_brand' => 'exists:brands,id',
            'codigo' => 'string|max:50|unique:products,codigo,' . $id,
            'name' => 'string|max:50',
            'description' => 'nullable|string|max:100',
            'imagen' => 'nullable|string|max:255',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product->update($request->except('categorias'));

        if ($request->has('categorias')) {
            $product->categorias()->sync($request->categorias);
        }

        return response()->json($product->load('categorias'));
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
