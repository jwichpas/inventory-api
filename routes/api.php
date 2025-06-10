<?php

use App\Http\Controllers\Api\Empresa\SelectEmpresaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\Empresa\EmpresaDataController;
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
use App\Http\Controllers\Api\Sire\ComprasController;
use App\Http\Controllers\Api\Sire\PeriodoController;
use App\Http\Controllers\Api\Sire\ReporteResumenController;
use App\Http\Controllers\Api\Sire\ReporteResumenDetalleController;
use App\Http\Controllers\Api\Sire\VentaResumenController;
use App\Http\Controllers\Api\TablaGeneral\TipoDocumentoController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionPleController;
use App\Http\Controllers\Api\TablaGeneral\TipoPrecioUnitarioController;
use App\Http\Controllers\Api\TablaGeneral\TipoAfectacionIgvController;
use App\Http\Controllers\Api\TablaGeneral\TipoOperacionController;
use App\Http\Controllers\Api\Sire\VentasController;
use App\Models\Sire\SireResumenVentas;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\Api\UserEmpresaController;
use App\Http\Controllers\Api\Factiliza\FactilizaController;
use App\Http\Controllers\Api\Inventario\AtributoController;
use App\Http\Controllers\Api\Inventario\TipoAtributoController;
use App\Http\Controllers\Api\Sire\ComprasArchivoController;
use App\Http\Controllers\Api\Sire\ComprasClasificacionController;
use App\Http\Controllers\Api\Sire\VentasArchivoController;
use App\Http\Controllers\Api\Sire\ComprasItemController;
use App\Http\Controllers\api\sire\SireDespatchAdviceController;
use App\Http\Controllers\Api\UserCompanyController;
use App\Http\Controllers\UserController;
use App\Models\TablaGeneral\TipoDocumento;
use App\Http\Controllers\Api\TablaGeneral\TipoDocumentoIdentidadController;
use App\Http\Controllers\Api\TablaGeneral\AnexoController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/registrar-empresa', [RegistroEmpresaController::class, 'registrar']);
Route::apiResource('empresas', EmpresaController::class);
Route::get('empresas-datos/{idEmpresa}', [EmpresaController::class, 'datos']);
Route::apiResource('unidad-medida', UnidadMedidaController::class);
Route::get('lista-undmedida/{idEmpresa}', [UnidadMedidaController::class, 'umedidaxempresa']);
Route::apiResource('brands', BrandController::class);
Route::get('lista-marcas/{idEmpresa}', [BrandController::class, 'marcaxempresa']);
Route::apiResource('categories', CategoryController::class);
Route::get('lista-categorias/{idEmpresa}', [CategoryController::class, 'categoriaxempresa']);
Route::apiResource('products', ProductController::class);
Route::post('nuevo-producto', [ProductController::class, 'guardar']);
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
Route::apiResource('tipo-doc-identidad', TipoDocumentoIdentidadController::class);

//Tipo de atributo
Route::apiResource('tipo-atributo', TipoAtributoController::class);

Route::apiResource('lista-atributo', AtributoController::class);
// Ruta adicional para obtener atributos por tipo
Route::get('/por-tipo/{idTipo}', [AtributoController::class, 'porTipo']);

//Categorias de productos
Route::post('/categories-validate/validate-code', [CategoryController::class, 'validateCode']);
Route::post('/brands/validate-code-brand', [BrandController::class, 'validateCode']);


Route::apiResource('compras-archivos/xml', ComprasArchivoController::class);
Route::apiResource('compras-items', ComprasItemController::class);
Route::get('/compras-detalles/{num_ruc}', [ComprasItemController::class, 'getComprasItems']);



// Ruta para listar VENTAS SIRE
Route::apiResource('ventas', VentasController::class);
Route::get('/ventas/ruc/{numRuc}', [VentasController::class, 'getVentasPorRuc']);
Route::get('/ventasmensual/pordia', [VentasController::class, 'ventasPorDiaMesActual']);
Route::get('/ventasmensual/pormes', [VentasController::class, 'ventasTotalesMes']);
Route::get('/ventasmensual/por-periodo', [VentasController::class, 'obtenerVentasPorPeriodo']);
Route::get('/ventas/facturas/ventas-faltantes', [VentasController::class, 'faltantes']);
Route::get('/resumen-ventas/calcular', [VentaResumenController::class, 'calcularResumenVentas']);
Route::apiResource('resumen-ventas', VentaResumenController::class);

Route::apiResource('ventas-archivos/xml', VentasArchivoController::class);

// Asignación de empresas a usuarios
Route::get('/usuarios', [UserController::class, 'index']);
Route::get('/usuarios/activos', [UserController::class, 'getActiveUsers']);
Route::get('/lista-empresas/activas', [EmpresaController::class, 'getActiveCompanies']);
// Obtener empresas asignadas a un usuario
Route::get('/empresas-usuario/{userId}', [UserCompanyController::class, 'getUserCompanies']);
Route::post('/user-companies/{userId}', [UserCompanyController::class, 'assignCompanies']);
Route::delete('/user-companies/{userId}', [UserCompanyController::class, 'removeAssignment']);

// Rutas para Anexos
Route::get('/anexos', [AnexoController::class, 'index']);
Route::post('/anexos', [AnexoController::class, 'store']);
Route::get('/anexos/{id}', [AnexoController::class, 'show']);
Route::put('/anexos/{id}', [AnexoController::class, 'update']);
Route::patch('/anexos/{id}', [AnexoController::class, 'update']);
Route::delete('/anexos/{id}', [AnexoController::class, 'destroy']);
Route::patch('/anexos/{id}/estado', [AnexoController::class, 'cambiarEstado']);

// Rutas de búsqueda
Route::get('/anexos/buscar/tipo', [AnexoController::class, 'getAnexosPorTipo']);
Route::get('/anexos/buscar/documento', [AnexoController::class, 'buscarPorDocumento']);


// Obtener la EMPRESA seleccionada
Route::post('/seleccion-empresa', [SelectEmpresaController::class, 'store']);
Route::get('/seleccion-empresa/{id}', [SelectEmpresaController::class, 'show']);
Route::put('/empresas/{empresa}/datos', [EmpresaDataController::class, 'updateEmpresaData']);
// Lista de empresas por usuario
Route::apiResource('empresayusuario', UserEmpresaController::class);
// FACTILIZA
Route::get('/factiliza/xml', [FactilizaController::class, 'getXml']);
Route::get('/factiliza/pdf', [FactilizaController::class, 'getPdf']);
Route::get('/factiliza/guia/xml', [FactilizaController::class, 'getGuiaXml']);

// Obtener PERIODOS SIRE
Route::apiResource('periodos', PeriodoController::class);
Route::post('/guardarperiodos/{id}', [PeriodoController::class, 'guardar']);
Route::get('/periodosporruc', [PeriodoController::class, 'obtenerEjerciciosFiscales']);
// Reporte de Cumplimiento SIRE
Route::apiResource('/reportecumplimiento/resumen', ReporteResumenController::class);
Route::apiResource('/reportecumplimiento/detalleresumen', ReporteResumenDetalleController::class);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Ruta para listar COMPRAS SIRE

    // Rutas protegidas

    /* Route::apiResource('movimientos', MovimientoController::class); */
});
Route::apiResource('compras', ComprasController::class);
Route::get('/compras/ruc/{ruc}', [ComprasController::class, 'comprasPorRuc']);
Route::get('/compras/proveedor', [ComprasController::class, 'comprasPorProveedor']);
Route::get('/compras-sire/por-periodo', [ComprasController::class, 'obtenerComprasPorPeriodo']);
Route::get('/compras/proveedor/compraspormes/{num_ruc}', [ComprasController::class, 'ComprasPorMesporPro']);

Route::get('/compras-inventarios/por-periodo', [ComprasController::class, 'obtenerInventarioPorPeriodo']);
//Guias de remisión SIRE
Route::apiResource('despatch-advices', SireDespatchAdviceController::class);
Route::get('despatch-advices/{id}/download-zip', [SireDespatchAdviceController::class, 'downloadZip']);


Route::prefix('compras')->group(function () {
    Route::post('/{compraId}/clasificaciones', [ComprasClasificacionController::class, 'store']);
    Route::put('/clasificaciones/{id}', [ComprasClasificacionController::class, 'update']);
    Route::get('/{compraId}/clasificaciones', [ComprasClasificacionController::class, 'show']);
});

Route::get('/compras/facturas/compras-faltantes', [ComprasController::class, 'faltantes']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::put('/empresas/{id}/token', [EmpresaController::class, 'updateToken']);
Route::get('/empresas/{id}/token', [EmpresaController::class, 'getToken']);
