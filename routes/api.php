<?php
<<<<<<< HEAD

use App\Http\Controllers\Api\Empresa\SelectEmpresaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\Empresa\EmpresaDataController;
=======
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EmpresaController;
>>>>>>> develop
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
<<<<<<< HEAD
use App\Http\Controllers\Api\Sire\ComprasController;
use App\Http\Controllers\Api\Sire\PeriodoController;
use App\Http\Controllers\Api\Sire\ReporteResumenController;
use App\Http\Controllers\Api\Sire\ReporteResumenDetalleController;
use App\Http\Controllers\Api\Sire\VentaResumenController;
=======
>>>>>>> develop
use App\Http\Controllers\Api\TablaGeneral\TipoDocumentoController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionPleController;
use App\Http\Controllers\Api\TablaGeneral\TipoPrecioUnitarioController;
use App\Http\Controllers\Api\TablaGeneral\TipoAfectacionIgvController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionController;
<<<<<<< HEAD
use App\Http\Controllers\Api\Sire\VentasController;
use App\Models\Sire\SireResumenVentas;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\Api\UserEmpresaController;
=======

>>>>>>> develop

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

<<<<<<< HEAD
//Categorias de productos
Route::post('/categories/validate-code', [CategoryController::class, 'validateCode']);
Route::post('/brands/validate-code-brand', [BrandController::class, 'validateCode']);

// Ruta para listar COMPRAS SIRE
Route::apiResource('compras', ComprasController::class);
Route::get('/compras/ruc/{ruc}', [ComprasController::class, 'comprasPorRuc']);
Route::get('/compras/proveedor', [ComprasController::class, 'comprasPorProveedor']);
Route::get('/compras-sire/por-periodo', [ComprasController::class, 'obtenerComprasPorPeriodo']);
Route::get('/compras/proveedor/compraspormes/{num_ruc}', [ComprasController::class, 'ComprasPorMesporPro']);

// Ruta para listar VENTAS SIRE
Route::apiResource('ventas', VentasController::class);
Route::get('/ventas/ruc/{numRuc}', [VentasController::class, 'getVentasPorRuc']);
Route::get('/ventasmensual/pordia', [VentasController::class, 'ventasPorDiaMesActual']);
Route::get('/ventasmensual/pormes', [VentasController::class, 'ventasTotalesMes']);
Route::get('/ventasmensual/por-periodo', [VentasController::class, 'obtenerVentasPorPeriodo']);
Route::get('/resumen-ventas/calcular', [VentaResumenController::class, 'calcularResumenVentas']);
Route::apiResource('resumen-ventas', VentaResumenController::class);

// Obtener la EMPRESA seleccionada
Route::post('/seleccion-empresa', [SelectEmpresaController::class, 'store']);
Route::get('/seleccion-empresa/{id}', [SelectEmpresaController::class, 'show']);
Route::put('/empresas/{empresa}/datos', [EmpresaDataController::class, 'updateEmpresaData']);
// Lista de empresas por usuario
Route::apiResource('empresayusuario', UserEmpresaController::class);


// Obtener PERIODOS SIRE
Route::apiResource('periodos', PeriodoController::class);
Route::post('/guardarperiodos/{id}', [PeriodoController::class, 'guardar']);
Route::get('/periodosporruc', [PeriodoController::class, 'obtenerEjerciciosFiscales']);
// Reporte de Cumplimiento SIRE
Route::apiResource('/reportecumplimiento/resumen', ReporteResumenController::class);
Route::apiResource('/reportecumplimiento/detalleresumen', ReporteResumenDetalleController::class);

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

=======
>>>>>>> develop
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/auth/logout', [AuthController::class, 'logout']);


    // Rutas protegidas

    /* Route::apiResource('movimientos', MovimientoController::class); */
});
