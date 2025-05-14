<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['empresa', 'brand', 'categorias', 'variantes'])->get();
        return response()->json($products);
    }

    public function guardar(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'empresa_id' => 'required|exists:empresas,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:products,code,NULL,id,empresa_id,' . $request->empresa_id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_type' => 'required|in:simple,variable',

            // Campos para producto simple
            'sku' => 'required_if:product_type,simple|string|max:100',
            'price' => 'required_if:product_type,simple|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'unit_id' => 'required_if:product_type,simple|exists:unidad_medida,id',
            'main_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',

            // Campos para producto variable
            'variants' => 'required_if:product_type,variable|array|min:1',
            'variants.*.sku' => 'required|string|max:100',
            'variants.*.sunat_code' => 'nullable|string|max:13',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'nullable|numeric|min:0',
            'variants.*.unit_id' => 'required|exists:unidad_medida,id',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.attribute_id' => 'required|exists:atributos,id',
            'variants.*.attributes.*.value_id' => 'required|exists:atributo_valores,id',
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
                'empresa_id',
                'name',
                'code',
                'description',
                'category_id',
                'brand_id',
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
                        'sku' => $variantData['sku'],
                        'sunat_code' => $variantData['sunat_code'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'unit_id' => $variantData['unit_id'],
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
                'data' => $product
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

    public function show($id)
    {
        $product = Product::with(['empresa', 'brand', 'categorias', 'variantes'])->find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
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
