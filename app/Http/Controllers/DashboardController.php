<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Importa tus modelos reales
use App\Models\Venta;          // tabla: ventas
use App\Models\Compra;         // tabla: compras
use App\Models\DetalleVenta;   // tabla: detalle_ventas
use App\Models\Producto;       // tabla: productos
use App\Models\Cliente;        // tabla: clientes
use App\Models\Caja;            // tabla: caja (ojo: singular en BD)

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Rango "mes actual" =====
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        // ===== KPIs =====
        $hoy = Carbon::today();
        $maniana = Carbon::tomorrow();

        // Ventas hoy (creadas hoy)
        $ventasHoy = (float) Venta::whereBetween('created_at', [$hoy, $maniana])
            ->sum('total');

        // Ventas del mes
        $ventasMes = (float) Venta::whereBetween('created_at', [$inicioMes, $finMes])
            ->sum('total');

        // Ingresos (ventas) y egresos (compras) del mes
        $ingresos = (float) Venta::whereBetween('created_at', [$inicioMes, $finMes])
            ->sum('total');

        $egresos  = (float) Compra::whereBetween('created_at', [$inicioMes, $finMes])
            ->sum('total');

        $utilidad = $ingresos - $egresos;

        // Productos en stock crítico (sin stock_minimo => umbral fijo)
        $UMBRAL_CRITICO = 5; // cámbialo si quieres
        $stockCritico = Producto::where('stock', '<=', $UMBRAL_CRITICO)->count();

        $totalProductos = Producto::count();
        $totalClientes  = Cliente::count();

        // ===== Gráfico 1: Ventas por mes (últimos 6) =====
        $vMes = Venta::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')->orderBy('ym','asc')->limit(6)->get();

        $ventasPorMes = [
            'labels' => $vMes->map(fn($r) => Carbon::parse($r->ym.'-01')->translatedFormat('M Y')),
            'data'   => $vMes->pluck('total')->map(fn($x)=>round((float)$x,2)),
        ];

        // ===== Gráfico 2: Top 10 productos más vendidos =====
        $top = DetalleVenta::selectRaw('productos.nombre AS prod, SUM(detalle_ventas.cantidad) AS cant')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->groupBy('productos.nombre')
            ->orderByDesc('cant')
            ->limit(10)
            ->get();

        $topProductos = [
            'labels' => $top->pluck('prod'),
            'data'   => $top->pluck('cant')->map(fn($x)=>(int)$x),
        ];

        // ===== Gráfico 3: Stock crítico vs suficiente =====
        $critico    = (int) Producto::where('stock','<=',$UMBRAL_CRITICO)->count();
        $suficiente = (int) Producto::where('stock','>',$UMBRAL_CRITICO)->count();
        $stock = [
            'labels' => ['Crítico/Bajo','Suficiente'],
            'data'   => [$critico, $suficiente],
        ];

        // ===== Gráfico 4: Ingresos vs Egresos (últimos 6 meses) =====
        $ing = Venta::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')->orderBy('ym','asc')->limit(6)->get();
        $egr = Compra::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')->orderBy('ym','asc')->limit(6)->get();

        $meses = collect($ing->pluck('ym'))->merge($egr->pluck('ym'))->unique()->sort()->values();

        $ingEgr = [
            'labels'  => $meses->map(fn($ym)=>Carbon::parse("$ym-01")->translatedFormat('M Y')),
            'ingresos'=> $meses->map(fn($ym)=> round((float)($ing->firstWhere('ym',$ym)->total ?? 0),2)),
            'egresos' => $meses->map(fn($ym)=> round((float)($egr->firstWhere('ym',$ym)->total ?? 0),2)),
        ];

        // ===== Gráfico 5: Tipos de pago (pie) =====
        // La tabla se llama "metodos_pago". La columna de nombre puede ser "nombre" o "metodo".
        $tiposPagoRows = DB::table('ventas')
            ->join('metodos_pago','metodos_pago.id','=','ventas.metodo_pago_id')
            ->selectRaw('COALESCE(metodos_pago.nombre, metodos_pago.nombre) AS tipo, COUNT(ventas.id) AS cnt')
            ->groupBy('tipo')
            ->orderByDesc('cnt')
            ->get();

        $tiposPago = [
            'labels' => $tiposPagoRows->pluck('tipo'),
            'data'   => $tiposPagoRows->pluck('cnt')->map(fn($x)=>(int)$x),
        ];

        // ===== Gráfico 6: Estado de cajas (barras) =====
        // Ojo: tu tabla es 'caja' (singular). El modelo Caja debe tener protected $table = 'caja';
        $cajas = [
            'labels' => ['Abiertas','Cerradas'],
            'data'   => [
                (int) Caja::where('estado','Abierto')->count(),
                (int) Caja::where('estado','Cerrado')->count(),
            ],
        ];

        return view('admin.dashboard.index', compact(
            'ventasHoy','ventasMes','ingresos','egresos','utilidad',
            'stockCritico','totalProductos','totalClientes',
            'ventasPorMes','topProductos','stock','ingEgr','tiposPago','cajas'
        ));
    }
}
