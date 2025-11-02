<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DetalleCompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FidelizacionController;

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Auth::routes();
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rutas solo para admins
    Route::get('/', function () {return view('admin.dashboard');})->name('dashboard');
    Route::resource('productos', ProductoController::class);
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::resource('compras', CompraController::class)->parameters(['compras' => 'compra']);
    Route::resource('detalleCompras', DetalleCompraController::class)->parameters(['detalleCompras' => 'detalleCompra']);
    Route::get('/detalleVentas/exportar', [DetalleVentaController::class, 'exportExcel'])->name('detalleVentas.exportar');
    Route::resource('ventas', VentaController::class)->parameters(['ventas' => 'venta']);
    Route::resource('detalleVentas', DetalleVentaController::class)->parameters(['detalleVentas' => 'detalleVenta']);
    Route::get('/clientes/buscar-por-dni', [ClienteController::class, 'buscarPorDni'])->name('clientes.buscarPorDni');
    Route::resource('cajas', CajaController::class)->parameters(['cajas' => 'caja']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Endpoints JSON para charts
    Route::get('/api/dashboard/ventas-30d',   [DashboardController::class, 'ventas30d']);
    Route::get('/api/dashboard/top-productos',[DashboardController::class, 'topProductos']);
    Route::get('/api/dashboard/tipos-pago',   [DashboardController::class, 'tiposPago']);
    Route::get('/api/dashboard/stock-resumen',[DashboardController::class, 'stockResumen']);

    Route::post('/ml/fidelizacion/predict', [FidelizacionController::class, 'predecir'])
    ->name('ml.fidelizacion.predict');
});

Route::middleware(['auth', 'empleado'])->prefix('empleado')->name('empleado.')->group(function () {
    // Rutas solo para empleados
    Route::resource('productos', ProductoController::class);
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::resource('compras', CompraController::class)->parameters(['compras' => 'compra']);
    Route::resource('detalleCompras', DetalleCompraController::class)->parameters(['detalleCompras' => 'detalleCompra']);
    Route::resource('ventas', VentaController::class)->parameters(['ventas' => 'venta']);
    Route::resource('detalleVentas', DetalleVentaController::class)->parameters(['detalleVentas' => 'detalleVenta']);
    Route::get('/clientes/buscar-por-dni', [ClienteController::class, 'buscarPorDni'])->name('clientes.buscarPorDni');

    Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
    Route::post('cajas/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::put('cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
    Route::get('cajas/{caja}', [CajaController::class, 'show'])->name('cajas.show'); // solo “view”
});
