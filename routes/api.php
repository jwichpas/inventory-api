<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\MovimientoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RegistroEmpresaController;

use App\Http\Controllers\Api\Inventario\UnidadMedidaController;
use App\Http\Controllers\Api\Inventario\BrandController;
use App\Http\Controllers\Api\Inventario\CategoryController;
use App\Http\Controllers\Api\Inventario\ProductController;
use App\Http\Controllers\Api\Inventario\VarianteProductoController;
use App\Http\Controllers\Api\Inventario\LoteController;
use App\Http\Controllers\Api\Inventario\AlmacenController;
use App\Http\Controllers\Api\Inventario\AlmacenStockController;
use App\Http\Controllers\Api\Inventario\MovimientoCabeceraController;
use App\Http\Controllers\Api\Inventario\MovimientoDetalleController;
use App\Http\Controllers\Api\TablaGeneral\TipoDocumentoController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionPleController;
use App\Http\Controllers\Api\TablaGeneral\TipoPrecioUnitarioController;
use App\Http\Controllers\Api\TablaGeneral\TipoAfectacionIgvController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionController;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/registrar-empresa', [RegistroEmpresaController::class, 'registrar']);
Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('unidad-medida', UnidadMedidaController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('variantes', VarianteProductoController::class);
Route::apiResource('lotes', LoteController::class);
Route::apiResource('almacenes', AlmacenController::class);
Route::apiResource('almacen-stock', AlmacenStockController::class)->except(['show', 'update', 'destroy']);
Route::put('almacen-stock/{id_variante}/{id_almacen}/{id_lote}', [AlmacenStockController::class, 'update']);
Route::delete('almacen-stock/{id_variante}/{id_almacen}/{id_lote}', [AlmacenStockController::class, 'destroy']);
Route::apiResource('movimiento-cabecera', MovimientoCabeceraController::class);
Route::apiResource('movimiento-detalle', MovimientoDetalleController::class);
Route::apiResource('tipo-documento', TipoDocumentoController::class);
Route::apiResource('tipo-operacion-ple', TipoOperacionPleController::class);
Route::apiResource('tipo-precio-unitario-fe', TipoPrecioUnitarioController::class);
Route::apiResource('catalogo-fe-07', TipoAfectacionIgvController::class);
Route::apiResource('catalogo-fe-17', TipoOperacionController::class);

//Categorias de productos
Route::post('/categories/validate-code', [CategoryController::class, 'validateCode']);
Route::post('/brands/validate-code-brand', [BrandController::class, 'validateCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/auth/logout', [AuthController::class, 'logout']);


    // Rutas protegidas

    /* Route::apiResource('movimientos', MovimientoController::class); */
});
